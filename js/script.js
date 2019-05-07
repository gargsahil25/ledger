$(document).ready(function() {
    if (getCookie('entry') == 'true') {
        $('#entryButton').click();
    }
    if (getCookie('hindi') == 'true') {
        $('#hindiButton').removeClass('collapsed');
    } else {
        $('#hindiButton').addClass('collapsed');
    }

    $('.header-menu').click(function(e) {
        var data = e.currentTarget.dataset;
        if ($(e.currentTarget).find('span').hasClass('collapsed')) {
            setCookie(data.cookie, true);
        } else {
            deleteCookie(data.cookie);
        }
        if (data.removecookie) {
            deleteCookie(data.cookie);
        }
        if (data.reload == "true") {
            window.location.reload();
        }
    });

    $('.txn-selector select, .txn-selector input').change(function(e) {
        $('.loader').fadeIn();
        var element = e.currentTarget;
        window.location.search = "?" + element.name + "=" + element.value;
    });

    $('input[name="txn-delete-confirm"]').on('click', function(e) {
        var index = e.currentTarget.dataset.index;
        var $form = $(this).closest('form');
        e.preventDefault();
        $('#confirm-' + index).modal({
                backdrop: 'static',
                keyboard: false
            })
            .one('click', '.delete', function(e) {
                $form.find('input[name="txn-delete-submit"]').val('Delete');
                $form.trigger('submit');
            });
    });

    $("form").submit(function(event) {
        $('.loader').fadeIn();
    });

    // Profit loss page
    for (var i = 0; i < $('.account-row').length; i++) {
        updateProfitLoss($($('.account-row')[i]));
    }
    updateTotalProfitLoss();

    $(".account-actualbalance").change(function(e) {
        updateProfitLoss($(e.currentTarget).closest('.account-row'));
        updateTotalProfitLoss();
    });
});

function updateProfitLoss($element) {
    var balance = $element.find(".account-balance").html();
    var actualBalance = $element.find(".account-actualbalance").val();
    var profit = parseInt(actualBalance) - parseInt(balance);
    $element.find(".account-profitloss").html(profit);

    var className = "";
    if (profit > 0) {
        className = "credit";
    } else if (profit < 0) {
        className = "debit";
    }
    $element.removeClass("credit");
    $element.removeClass("debit");
    $element.addClass(className);
}

function updateTotalProfitLoss() {
    var total = 0;
    for (var i = 0; i < $('.account-profitloss').length; i++) {
        total += parseInt($($('.account-profitloss')[i]).html());
    }
    if (total >= 0) {
        $(".totalprofitloss").addClass("green");
    } else {
        $(".totalprofitloss").removeClass("green");
    }
    $(".totalprofitloss").html(formatMoney(total));
}

function setCookie(cname, cvalue, exdays) {
    exdays = exdays || 1000;
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

var deleteCookie = function(name) {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
};

function formatMoney(num) {
    var n1, n2;
    num = num + '' || '';
    n1 = num.split('.');
    n2 = n1[1] || null;
    n1 = n1[0].replace(/(\d)(?=(\d\d)+\d$)/g, "$1,");
    num = n2 ? n1 + '.' + n2 : n1;
    return "&#8377; " + num;
};