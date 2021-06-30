<?php 

include_once "constant.php";
include_once "mysql.php";

/*
    {
        credit: {
            userName: {
                dateRange: {
                    value: number,
                    txns: array
                }
            }
        }
    }
*/
function getDataForStats() {
    global $ACCOUNT_TYPE;
    $txns = getTransactionsForAllUsers();
    $data = initialiseData();
    
    foreach($txns as $t) {
        $fromAccType = $t['from_account_type'];
        $toAccType = $t['to_account_type'];
        $amount = $t['amount'];

        if ($fromAccType == $ACCOUNT_TYPE['HOME_EXPENSE'] || $toAccType == $ACCOUNT_TYPE['HOME_EXPENSE']) {
            $value = $toAccType == $ACCOUNT_TYPE['HOME_EXPENSE'] ? $amount : $amount * -1;
            $chartData = addTxnToData($data['home_expense'], $t, $value);
            $data['home_expense'] = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['BUSINESS_EXPENSE'] || $toAccType == $ACCOUNT_TYPE['BUSINESS_EXPENSE']) {
            $value = $toAccType == $ACCOUNT_TYPE['BUSINESS_EXPENSE'] ? $amount : $amount * -1;
            $chartData = addTxnToData($data['business_expense'], $t, $value);
            $data['business_expense'] = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['CAPITAL'] || $toAccType == $ACCOUNT_TYPE['CAPITAL']) {
            $value = $fromAccType == $ACCOUNT_TYPE['CAPITAL'] ? $amount : $amount * -1;
            $chartData = addTxnToData($data['capital'], $t, $value);
            $data['capital'] = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['BUSINESS_PROPERTY'] || $toAccType == $ACCOUNT_TYPE['BUSINESS_PROPERTY']) {
            $value = $toAccType == $ACCOUNT_TYPE['BUSINESS_PROPERTY'] ? $amount : $amount * -1;
            $chartData = addTxnToData($data['factory_property'], $t, $value);
            $data['factory_property'] = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['CLIENT'] && $toAccType == $ACCOUNT_TYPE['CASH']) {
            $value = $amount;
            $chartData = addTxnToData($data['credit'], $t, $value);
            $data['credit'] = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['CASH'] && $toAccType == $ACCOUNT_TYPE['CLIENT']) {
            $value = $amount;
            $chartData = addTxnToData($data['debit'], $t, $value);
            $data['debit'] = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['STOCK']) {
            $value = $amount;
            $chartData = addTxnToData($data['sale'], $t, $value);
            $data['sale'] = $chartData;

        } else if ($toAccType == $ACCOUNT_TYPE['STOCK']) {
            $value = $amount;
            $chartData = addTxnToData($data['purchase'], $t, $value);
            $data['purchase'] = $chartData;
        }
	}
    return $data;
}

function initialiseData() {
    $data = array(
        'home_expense' => array(),
        'business_expense' => array(),
        'capital' => array(),
        'factory_property' => array(),
        'credit' => array(),
        'debit' => array(),
        'sale' => array(),
        'purchase' => array()
    );

    $users = getAllUsers();
    foreach($data as $t => $tData) {
        foreach($users as $user) {
            $u = $user['name'];
            $data[$t][$u] = array();
            $start = $month = strtotime('2021-04-01');
            $end = strtotime('2021-10-01');
            while($month < $end)
            {
                $d = date('M Y', $month);
                if (!isset($data[$t][$u][$d])) {
                    $data[$t][$u][$d] = array(
                        "value" => 0,
                        "txns" => array()
                    );
                }
                $month = strtotime("+1 month", $month);
            }
        }
    }
    return $data;
}

function addTxnToData($chartData, $t, $value) {
    $userName = $t['user_name'];
    $txnDate = $t['date'];

    if (!isset($chartData[$userName])) {
        $chartData[$userName] = array();
    }
    $userData = $chartData[$userName];

    $dataRange = getDateRange($txnDate);
    if (!isset($userData[$dataRange])) {
        $userData[$dataRange] = array();
    }
    $dateRangeData = $userData[$dataRange];
    
    if (!isset($dateRangeData['txns'])) {
        $dateRangeData['txns'] = array();
    }
    array_push($dateRangeData['txns'], $t);

    if (!isset($dateRangeData['value'])) {
        $dateRangeData['value'] = 0;
    }
    $dateRangeData['value'] += $value;

    $userData[$dataRange] = $dateRangeData;
    $chartData[$userName] = $userData;
    return $chartData;
}

function getDateRange($date) {
    return date('M Y', strtotime($date));
}

function renderChart($containerId, $title, $data) {
    $script = 'new CanvasJS.Chart("'.$containerId.'", {
        theme: "light2",
        animationEnabled: true,
        title:{
            text: "'.$title.'"
        },
        axisY:{
            prefix: "₹ ",
            suffix: "K"
        },
        legend:{
            cursor: "pointer",
            dockInsidePlotArea: true
        },
        data: [';
        
    foreach($data as $userName => $userData) {
        $chartData = getChartData($userData);
        $script .= '{
            type: "line",
            name: "'.$userName.'",
            markerSize: 0,
            toolTipContent: "Date: {label} <br>{name}: ₹ {y}K <br> {txns}",
            showInLegend: true,
            dataPoints: '.json_encode($chartData, JSON_NUMERIC_CHECK).'
        },';
    }
    
    $script = substr($script, 0, -1);
    $script .= ']}).render();';
    echo $script;
}

function getChartData($userData) {
    $chartData = array();
    foreach($userData as $dateRange => $dateRangeData) {
        array_push($chartData, array(
            "label" => $dateRange,
            "y" => ($dateRangeData['value'] / 1000)
        ));
    }
    return $chartData;
}

function displayStatsTxnTypes($data, $txnType) {
	echo '<option value="">Txn Type</option>';
    foreach($data as $t => $tData) {
        if ($t == $txnType) {
            echo '<option selected value="'.$t.'">'.$t.'</option>';
        } else {
            echo '<option value="'.$t.'">'.$t.'</option>';
        }
    }
}

function displayStatsUsers($data, $txnType, $userName) {
	echo '<option value="">'.getLangText("SELECT_USER").'</option>';
    if ($txnType != null) {
        foreach($data[$txnType] as $u => $uData) {
            if ($userName == $u) {
                echo '<option selected value="'.$u.'">'.$u.'</option>';
            } else {
                echo '<option value="'.$u.'">'.$u.'</option>';
            }
        }
    }
}

function displayStatsDateRanges($data, $txnType, $userName, $dateRange) {
	echo '<option value="">Date Range</option>';
    if ($txnType != null && $userName != null) {
        foreach($data[$txnType][$userName] as $d => $dData) {
            if ($dateRange == $d) {
                echo '<option selected value="'.$d.'">'.$d.'</option>';
            } else {
                echo '<option value="'.$d.'">'.$d.'</option>';
            }
        }
    }
}

function getStatsTxnData($data, $txnType, $userName, $dateRange) {
    if ($txnType != null && $userName != null && $dateRange != null) {
        return $data[$txnType][$userName][$dateRange];
    }
    return null;
}



?>