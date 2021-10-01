<?php 

include_once "constant.php";
include_once "mysql.php";
include_once "sessionUtil.php";

function displayReport($userDetail, $overrideProfit) {
    $profitPercent = $userDetail['profit'];
    if ($overrideProfit != null) {
        $profitPercent = $overrideProfit;
    }
    $report = getDataForReport($userDetail['id'], $profitPercent);
    $months = array_keys($report);
    echo "<table class='report'><tr><th>&nbsp;</th>";
    foreach ($months as $m) {
        echo "<th>".ucfirst($m)."</th>";
    }
    echo "</tr>";
    displayRow($report, $months, 'Capital', 'capital');
    displayRow($report, $months, 'Purchase', 'purchase');
    displayRow($report, $months, 'Sale', 'sale');
    displayRow($report, $months, 'Profit ('.$profitPercent.'%)', 'profit');
    displayRow($report, $months, 'Home Expense', 'home_expense');
    displayRow($report, $months, 'Business Expense', 'business_expense');
    displayRow($report, $months, 'Business Property', 'factory_property');
    displayRow($report, $months, '<span title="Profit - Business Expense">Actual Profit</span>', 'actual_profit');
    displayRow($report, $months, 'Credit Balance', 'credit_balance');
    displayRow($report, $months, 'Debit Balance', 'debit_balance');
    displayRow($report, $months, 'Cash Balance', 'cash_balance');
    displayRow($report, $months, '<span title="Stock + Profit">Actual Stock Balance</span>', 'stock_balance');
    displayRow($report, $months, 'Total Capital', 'cum_capital');
    displayRow($report, $months, '<span title="Capital + Profit - Business Expense - Home Expense">Net Capital Balance</span>', 'cum_balance_capital');
    echo "</table>";
}

function displayRow($report, $months, $title, $type) {
    echo "<tr><th>".$title."</th>";
    foreach ($months as $m) {
        echo "<td>".getMoneyFormat(getValue($report[$m], $type), true);
        displayTxns($report[$m], $type);
        echo "</td>";
    }
    echo "</tr>";
}

function getDataForReport($userId, $profitPercent) {
    $txns = getTransactionsByUserId($userId);
    $monthWiseData = getFormattedData($txns);
    $totalData = array(
        'home_expense' => array('value' => 0),
        'business_expense' => array('value' => 0),
        'capital' => array('value' => 0),
        'factory_property' => array('value' => 0),
        'sale' => array('value' => 0),
        'purchase' => array('value' => 0),
        'profit' => array('value' => 0),
        'actual_profit' => array('value' => 0),
        'credit_balance' => array('value' => 0),
        'debit_balance' => array('value' => 0),
        'cash_balance' => array('value' => 0),
        'stock_balance' => array('value' => 0),
        'cum_capital' => array('value' => 0),
        'cum_balance_capital' => array('value' => 0)
    );
    $cumCapital = 0;
    $cumProfit = 0;
    $cumHomeExp = 0;
    $cumBizExp = 0;

    foreach($monthWiseData as $dataRange => $monthlyData) {
        $profit = (int)($profitPercent * getValue($monthlyData, 'sale') / 100);
        $monthlyData['profit'] = array('value' => $profit);
        $monthlyData['actual_profit'] = array('value' => getValue($monthlyData, 'profit') - getValue($monthlyData, 'business_expense'));
        foreach($monthlyData as $type => $typeData) {
            $totalData[$type]['value'] += getValue($monthlyData, $type);
        }

        $cumCapital += getValue($monthlyData, 'capital');
        $cumProfit += getValue($monthlyData, 'profit');
        $cumHomeExp += getValue($monthlyData, 'home_expense');
        $cumBizExp += getValue($monthlyData, 'business_expense');
        $monthlyData['cum_capital'] =  array('value' => $cumCapital);
        $monthlyData['cum_profit'] =  array('value' => $cumProfit);
        $monthlyData['cum_home_expense'] =  array('value' => $cumHomeExp);
        $monthlyData['cum_business_expense'] =  array('value' => $cumBizExp);
        $monthlyData['cum_balance_capital'] =  array('value' => $cumCapital + $cumProfit - $cumHomeExp - $cumBizExp);
        $monthWiseData[$dataRange] = $monthlyData;
    }

    $accBalance = getAccountsBalance();
    $accBalance['stock_balance']['value'] += $totalData['profit']['value'];

    $monthWiseData['total'] = $totalData;
    $monthWiseData['total']['credit_balance'] = $accBalance['credit_balance'];
    $monthWiseData['total']['debit_balance'] = $accBalance['debit_balance'];
    $monthWiseData['total']['cash_balance'] = $accBalance['cash_balance'];
    $monthWiseData['total']['stock_balance'] = $accBalance['stock_balance'];
    $monthWiseData['total']['cum_capital']['value'] = $cumCapital;
    $monthWiseData['total']['cum_balance_capital']['value'] = $cumCapital + $cumProfit - $cumHomeExp - $cumBizExp;

    return $monthWiseData;
}

function getValue($data, $key) {
    if (isset($data[$key])) {
        return $data[$key]['value'];
    }
    return 0;
}

function displayTxns($data, $key) {
    if (!isset($data[$key]) || !isset($data[$key]['typeData'])) {
        return;
    }
    $typeData = $data[$key]['typeData'];
    echo "<table class='report-detail'>";
    foreach($typeData as $accountId => $accData) {
        echo "<tr><td><a target='_blank' href='/index.php?txn-account=".$accountId."'>".getLangText($accData['accountName'])."</a></td><td>".getMoneyFormat($accData['value'], true)."</td></tr>";
    }
    echo "</table>";
}

function getAccountsBalance() {
    global $ACCOUNT_TYPE;
    $accounts = getAccounts();
    $accBalance = array(
        "credit_balance" => array('value' => 0, 'typeData' => array()),
        "debit_balance" => array('value' => 0, 'typeData' => array()),
        "cash_balance" => array('value' => 0, 'typeData' => array()),
        "stock_balance" => array('value' => 0, 'typeData' => array())
    );
	foreach($accounts as $account) {
        if ($account['type'] ==  $ACCOUNT_TYPE['CASH']) {
            $accBalance['cash_balance']['value'] += $account['balance'];
            $accBalance['cash_balance']['typeData'][$account['id']] = array("accountName" => $account['name'], 'value' => $account['balance']);
        }

        if ($account['type'] ==  $ACCOUNT_TYPE['STOCK']) {
            $accBalance['stock_balance']['value'] += $account['balance'];
            $accBalance['stock_balance']['typeData'][$account['id']] = array("accountName" => $account['name'], 'value' => $account['balance']);
        }

		if ($account['type'] ==  $ACCOUNT_TYPE['CLIENT'] && $account['balance'] > 0) {
            $accBalance['credit_balance']['value'] += $account['balance'];
            $accBalance['credit_balance']['typeData'][$account['id']] = array("accountName" => $account['name'], 'value' => $account['balance']);
        }

        if ($account['type'] ==  $ACCOUNT_TYPE['CLIENT'] && $account['balance'] < 0) {
            $accBalance['debit_balance']['value'] += $account['balance'];
            $accBalance['debit_balance']['typeData'][$account['id']] = array("accountName" => $account['name'], 'value' => $account['balance']);
        }
	}

    return $accBalance;
}


/*
    {
        dateRange: {
            type: {
                value: number,
                typeData: {
                    accountId: {
                        accountName: string,
                        value: number,
                        txns: array
                    }
                }
            }
        }
    }
*/
function getFormattedData($txns) {
    global $ACCOUNT_TYPE;
    $data = array();
    
    foreach($txns as $t) {
        $fromAccType = $t['from_account_type'];
        $toAccType = $t['to_account_type'];
        $amount = $t['amount'];

        if ($fromAccType == $ACCOUNT_TYPE['HOME_EXPENSE'] || $toAccType == $ACCOUNT_TYPE['HOME_EXPENSE']) {
            $account = getAccount($t, $ACCOUNT_TYPE['HOME_EXPENSE']);
            $value = $toAccType == $ACCOUNT_TYPE['HOME_EXPENSE'] ? $amount : $amount * -1;
            $chartData = addTxnToData($data, 'home_expense', $t, $value, $account['id'], $account['name']);
            $data = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['BUSINESS_EXPENSE'] || $toAccType == $ACCOUNT_TYPE['BUSINESS_EXPENSE']) {
            $account = getAccount($t, $ACCOUNT_TYPE['BUSINESS_EXPENSE']);
            $value = $toAccType == $ACCOUNT_TYPE['BUSINESS_EXPENSE'] ? $amount : $amount * -1;
            $chartData = addTxnToData($data, 'business_expense', $t, $value, $account['id'], $account['name']);
            $data = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['CAPITAL'] || $toAccType == $ACCOUNT_TYPE['CAPITAL']) {
            $account = getAccount($t, $ACCOUNT_TYPE['CAPITAL']);
            $value = $fromAccType == $ACCOUNT_TYPE['CAPITAL'] ? $amount : $amount * -1;
            $chartData = addTxnToData($data, 'capital', $t, $value, $account['id'], $account['name']);
            $data = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['BUSINESS_PROPERTY'] || $toAccType == $ACCOUNT_TYPE['BUSINESS_PROPERTY']) {
            $account = getAccount($t, $ACCOUNT_TYPE['BUSINESS_PROPERTY']);
            $value = $toAccType == $ACCOUNT_TYPE['BUSINESS_PROPERTY'] ? $amount : $amount * -1;
            $chartData = addTxnToData($data, 'factory_property', $t, $value, $account['id'], $account['name']);
            $data = $chartData;

        } else if ($fromAccType == $ACCOUNT_TYPE['STOCK']) {
            $accountId = $t['to_account_id'];
            $accountName = $t['to_account_name']; 
            $value = $amount;
            $chartData = addTxnToData($data, 'sale', $t, $value, $accountId, $accountName);
            $data = $chartData;

        } else if ($toAccType == $ACCOUNT_TYPE['STOCK']) {
            $accountId = $t['from_account_id'];
            $accountName = $t['from_account_name'];
            $value = $amount;
            $chartData = addTxnToData($data, 'purchase', $t, $value, $accountId, $accountName);
            $data = $chartData;
        }
	}
    return $data;
}

function getAccount($t, $type) {
    $accountId = $t['from_account_id'];
    $accountName = $t['from_account_name'];
    if ($t['to_account_type'] == $type) {
        $accountId = $t['to_account_id'];
        $accountName = $t['to_account_name'];
    }
    return array('id' => $accountId, 'name' => $accountName);
}

function addTxnToData($dateObj, $type, $t, $value, $accountId, $accountName) {
    $txnDate = $t['date'];
    $dataRange = getDateRange($txnDate);
    if (!isset($dateObj[$dataRange])) {
        $dateObj[$dataRange] = array();
    }
    if(!isset($dateObj[$dataRange][$type])) {
        $dateObj[$dataRange][$type] = array('value' => 0, 'typeData' => array());
    }
    
    $typeData = $dateObj[$dataRange][$type]['typeData'];
    if (!isset($typeData[$accountId])) {
        $typeData[$accountId] = array('value' => 0, 'accountName' => $accountName, 'txns' => array());
    }
    $typeData[$accountId]['value'] += $value;
    array_push($typeData[$accountId]['txns'], $t);

    $dateObj[$dataRange][$type]['value'] += $value;
    $dateObj[$dataRange][$type]['typeData'] = $typeData;
    return $dateObj;
}

function getDateRange($date) {
    return date('M Y', strtotime($date));
}


?>