function notify(json) {
    var data = "";
    var title = "";
    try {
        data = JSON.parse(json);
    }
    catch (e) {
        json = JSON.stringify({type: "danger", message: "Error receive type data"});
        data = JSON.parse(json);
    }

    switch (data.type) {
        case "info":
            title = "info";
            break;

        case "success":
            title = "success";
            break;

        case "danger":
            title = "danger";
            break;
    }

    $.notify({
        icon: 'glyphicon glyphicon-warning-sign',
        title: title + "!",
        message: data.message
    }, {
        // settings
        element: 'body',
        position: null,
        type: data.type,
        allow_dismiss: false,
        newest_on_top: true,
        showProgressbar: false,
        placement: {
            from: "top",
            align: "right"
        },
        offset: 20,
        spacing: 10,
        z_index: 9999,
        delay: 5000,
        timer: 1000,
        url_target: '_blank',
        mouse_over: 'pause',
        animate: {
            enter: 'animated fadeInDown',
            exit: 'animated fadeOutUp'
        },
        onShow: null,
        onShown: null,
        onClose: null,
        onClosed: null,
        icon_type: 'class',
        template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
        '<button type="button" aria-hidden="true" class="close" data-notify="dismiss"><span aria-hidden="true">&times;</span></button>' +
        '<span data-notify="icon"></span> ' +
        '<span data-notify="title"><b>{1}</b></span><br>' +
        '<span data-notify="message">{2}</span>' +
        '<div class="progress" data-notify="progressbar">' +
        '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
        '</div>' +
        '<a href="{3}" target="{4}" data-notify="url"></a>' +
        '</div>'
    });
}

$('button[name=authUser]').on('click', function () {
    var username = $('input[name=username]').val();
    var password = $('input[name=password]').val();

    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=auth",
        data: {username: username, password: password},
        success: function (data) {
            // $("#systemMessage").html(data);
            var data1 = JSON.parse(data);
            if (data1.type === "reload") location.reload(); else notify(data);
        }
    });
});

$('button[name=sendXML]').on('click', function () {
    var id = $("input[name=id]").val();
    var xml = editor.getValue();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=sendxml",
        data: {id: id, xml: xml},
        success: function (data) {
            //$("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=sendMail]').on('click', function () {
    var idChar = $('#idChar').val();
    var idItem = $('#idItem').val();
    var countItem = $('#countItem').val();
    var maxCountItem = $('#maxCountItem').val();
    var octetItem = $('#octetItem').val();
    var prototypeItem = $('#prototypeItem').val();
    var timeItem = $('#timeItem').val();
    var maskItem = $('#maskItem').val();
    var moneyItem = $('#moneyItem').val();
    var titleItem = $('#titleItem').val();
    var messageItem = $('#messageItem').val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=sendmail",
        data: {
            idChar: idChar,
            idItem: idItem,
            countItem: countItem,
            maxCountItem: maxCountItem,
            octetItem: octetItem,
            prototypeItem: prototypeItem,
            timeItem: timeItem,
            maskItem: maskItem,
            moneyItem: moneyItem,
            titleItem: titleItem,
            messageItem: messageItem
        },
        success: function (data) {
            //$("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=sendMailOnline]').on('click', function () {
    var idItem = $('#idItem').val();
    var countItem = $('#countItem').val();
    var maxCountItem = $('#maxCountItem').val();
    var octetItem = $('#octetItem').val();
    var prototypeItem = $('#prototypeItem').val();
    var timeItem = $('#timeItem').val();
    var maskItem = $('#maskItem').val();
    var moneyItem = $('#moneyItem').val();
    var titleItem = $('#titleItem').val();
    var messageItem = $('#messageItem').val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=sendmailall",
        data: {
            idItem: idItem,
            countItem: countItem,
            maxCountItem: maxCountItem,
            octetItem: octetItem,
            prototypeItem: prototypeItem,
            timeItem: timeItem,
            maskItem: maskItem,
            moneyItem: moneyItem,
            titleItem: titleItem,
            messageItem: messageItem
        },
        success: function (data) {
            $("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=saveVisual]').on('click', function () {
    var save = {};
    var id = $("input[name=id]").val();
    $.each($('textarea,input[name^="visual\\["],select[name^="visual\\["]').serializeArray(), function () {
        var visual = this.name.replace(/visual[]/, '');
        save[visual] = this.value;
    });
    $.ajax({
        url: adr + "/index.php?function=sendvisual",
        type: "POST",
        data: {
            id: id,
            visual: save
        },
        success: function (data) {
            //$("#systemMessage").html(data)
            notify(data);
        }
    });
});

$('button[name=sendChatMsg]').on('click', function () {
    var msg = $("input[name=chatMsg]").val();
    var chanel = $("select[name=chatChanel]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=sendmsg",
        data: {msg: msg, chanel: chanel},
        success: function (data) {
            notify(data);
        }
    });
});

$('button[name=startServer]').on('click', function () {
    $.ajax({
        type: "POST", url: adr + "/index.php?function=startServer", success: function (data) {
            //$("#systemMessage").html(data);
            notify(data);
        }
    });
});

$('button[name=stopServer]').on('click', function () {
    $.ajax({
        type: "POST", url: adr + "/index.php?function=stopServer", success: function (data) {
            notify(data);
        }
    });
});

$('button[name=restartServer]').on('click', function () {
    $.ajax({
        type: "POST", url: adr + "/index.php?function=restartServer", success: function (data) {
            notify(data);
        }
    });
});

function goTeleport() {
    var id = $("input[name=charIDFunc]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=teleport",
        data: {id: id},
        success: function (data) {
            notify(data);

        }
    });
}

function kickRole(id) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=kickrole",
        data: {id: id},
        success: function (data) {
            notify(data);
        }
    });
}

function goNullChar() {
    var id = $("input[name=charIDFunc]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=nullchar",
        data: {id: id},
        success: function (data) {
            notify(data);
        }
    });
}

function goDelChar(ida) {
    var id;
    if (ida === "") id = $("input[name=charIDFunc]").val(); else id = ida;
    var result = confirm("Вы точно хотите удалить персонажа с ID " + id);
    if (result) {
        $.ajax({
            type: "POST",
            url: adr + "/index.php?function=delrole",
            data: {id: id},
            success: function (data) {
                notify(data);
            }
        });
    }
}

function goRenameRole() {
    var id = $("input[name=renameIDRole]").val();
    var oldName = $("input[name=renameOldNameRole]").val();
    var newName = $("input[name=renameNewNameRole]").val();

    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=renamerole",
        data: {id: id, oldname: oldName, newname: newName},
        success: function (data) {
            notify(data);
        }
    });

}

function goLevelUp() {
    var id = $("input[name=charIDLevelUp]").val();
    var level = $("input[name=charLevelUp]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=levelup",
        data: {id: id, level: level},
        success: function (data) {
            notify(data);

        }
    });
}

function goAddCash() {
    var id = $("input[name=charIDCash]").val();
    var gold = $("input[name=countCash]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=addcash",
        data: {id: id, gold: gold},
        success: function (data) {
            notify(data);
        }
    });
}

function goEdit(type) {
    var id = $("input[name=charIDEdit]").val();
    if (type == "xml") location.href = adr + "/?controller=editor&page=xml&id=" + id; else location.href = adr + "/?controller=editor&id=" + id;
}

function tryint(int) {
    var objAr = [];
    while (int != 0) {
        if (int >= 262144) {
            int -= 131072;
            objAr.push(131072);
        }
        if (int >= 131072 && int < 262144) {
            int -= 131072;
            objAr.push(131072);
        }
        if (int >= 65536 && int < 131072) {
            int -= 65536;
            objAr.push(65536);
        }
        if (int >= 32768 && int < 65536) {
            int -= 32768;
            objAr.push(32768);
        }
        if (int >= 16384 && int < 32768) {
            int -= 16384;
            objAr.push(16384);
        }
        if (int >= 8192 && int < 16384) {
            int -= 8192;
            objAr.push(8192);
        }
        if (int >= 4096 && int < 8192) {
            int -= 4096;
            objAr.push(4096);
        }
        if (int >= 2048 && int < 4096) {
            int -= 2048;
            objAr.push(2048);
        }
        if (int >= 1024 && int < 2048) {
            int -= 1024;
            objAr.push(1024);
        }
        if (int >= 512 && int < 1024) {
            int -= 512;
            objAr.push(512);
        }
        if (int >= 256 && int < 512) {
            int -= 256;
            objAr.push(256);
        }
        if (int >= 128 && int < 256) {
            int -= 128;
            objAr.push(128);
        }
        if (int >= 64 && int < 128) {
            int -= 64;
            objAr.push(64);
        }
        if (int >= 32 && int < 64) {
            int -= 32;
            objAr.push(32);
        }
        if (int >= 16 && int < 32) {
            int -= 16;
            objAr.push(16);
        }
        if (int >= 8 && int < 16) {
            int -= 8;
            objAr.push(8);
        }
        if (int >= 4 && int < 8) {
            int -= 4;
            objAr.push(4);
        }
        if (int >= 2 && int < 4) {
            int -= 2;
            objAr.push(2);
        }
        if (int >= 1 && int < 2) {
            int -= 1;
            objAr.push(1);
        }
    }
    return objAr;
}

function modal_mask() {
    var int = $("#prototypeItem").val();
    var objMask = tryint(int);
    $.each(objMask, function (indexrr, value) {
        $("input[name=mask_" + value + "]").attr('checked', true);
    });

    var result = 0;
    $("#accept").on("click", function () {
        $("input[class='custom-checkbox']:checked").each(function () {
            result = result + parseInt($(this).val());
        });
        $("#prototypeItem").val(result);
        $("#windows").html("").css({'display': 'none'});
        result = 0;

    });
}

function getChars(accountID) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=getrole",
        data: {id: accountID},
        success: function (data) {
            $("#charListModal").html(data);
        }
    });
}

function addCash(accountID) {
    $("#acceptGold").on("click", function () {
        var gold = $("input[name=goldCountSend]").val();
        $.ajax({
            type: "POST",
            url: adr + "/index.php?function=addcash",
            data: {id: accountID, gold: gold},
            success: function (data) {
                notify(data);
            }
        });
    });
}

function editGM(accountID) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=checkGM",
        data: {id: accountID},
        success: function (data) {

            data = JSON.parse(data);
            $.each(data, function (key, value) {
                $("input[name=gm-" + value + "]").attr("checked", "checked");
            });
            $("button[name=selectall]").on('click', function () {
                $('input:checkbox').attr('checked', "checked");
            });

            $("button[name=unselectall]").on('click', function () {
                $('input:checkbox').removeAttr('checked');
            });

            $("#addGM").on("click", function () {
                var checkedValue = {};
                var inputElements = document.getElementsByClassName('custom-checkbox');
                for (var i = 0; inputElements[i]; ++i) {
                    if (inputElements[i].checked) checkedValue[i] = inputElements[i].value;
                }
                $.ajax({
                    type: "POST",
                    url: adr + "/index.php?function=managerGM",
                    data: {id: accountID, params: checkedValue},
                    success: function (data) {
                        notify(data);
                    }
                });
            });
        }
    });
}

function pTypeItem() {
    var int = $('input[name="item[proctype]"]').val();
    var objMask = tryint(int);
    $.each(objMask, function (indexrr, value) {
        $("input[name=mask_" + value + "]").attr('checked', true);
    });

    var result = 0;
    $("#accept").on("click", function () {
        $("input[class='custom-checkbox']:checked").each(function () {
            result = result + parseInt($(this).val());
        });
        $('input[name="item[proctype]"]').val(result);
        result = 0;

    });
}


function editPocketItem(idKey){
    var input = $("input[data-name=packetItems]");
    var getJson = input.val();
    var json = JSON.parse(getJson);
    console.log(this);

    $.each(json[idKey], function (a,b) {
        $('input[name="item['+a+']"]').val(b);
    });

    $("#acceptDel").on('click', function () {
        $("[data-key=" + idKey + "]").remove();
        console.log(idKey);
        delete json[idKey];
        //$('body>.tooltip').remove();
        input.val(JSON.stringify(json));
    });

    $("#acceptSave").on('click', function () {
        //input.val(JSON.stringify(json));
        $.each($('input[name^="item\\["]').serializeArray(), function () {
            var visual = this.name.match(/item\[(.*)\]/)[1];
            json[idKey][visual] = this.value;
        });
        input.val(JSON.stringify(json));
    });
}

function selectItemMail() {
    var id = $("select[name=lastItems]").val();
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=getmailitems",
        data: {id: id},
        success: function (data) {
            //$("#systemMessage").html(data)
            data = JSON.parse(data);
            $.each(data, function (key, value) {
                $("#" + key).val(value);
            })
        }
    });
}

function goRead() {
    var loader = $(".loaderEl");

    loader.html("<b>Читаем элемент...</b>");
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=goreadelement",
        success: function (data) {
            //$("#systemMessage").html(data)
            loader.html("");
            notify(data);
        }
    });
}

function uploadIconItems() {
    var loader = $(".loaderIc");
    loader.html("<b>Разделяем и загружаем иконки...</b>");
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=gouploadicon",
        success: function (data) {
            // $("#systemMessage").html(data)
            loader.html("");
            notify(data);
        }
    });
}

function killPid(pid) {
    $.ajax({
        type: "POST",
        url: adr + "/index.php?function=killpid",
        data: {pid: pid},
        success: function (data) {
            //$("#systemMessage").html(data);
            // loader.html("");
            notify(data);
        }
    });
}

var statusChatBlock = true;

function chengeChl(id, type) {
    if (type === undefined) {
        if (statusChatBlock === true) {
            $(".chatW").show();
            $(".chatWindow").removeClass("hideChatWindow");
            $(".chatHead").removeClass("hideChatWindow");
            statusChatBlock = false;
        }
    }
    var chat = $("[data-target=chatMessage]");
    chat.attr('data-chl', id);
    startChat();
    chat.scrollTop(chat.prop('scrollHeight'));
}

function startChat() {
    var chat = $("[data-target=chatMessage]");
    $.ajax({
        url: adr + '/index.php?function=getchat&chl=' + chat.attr('data-chl'),
        type: 'GET',
        success: function (data) {
            chat.html(data);
        }
    });

}

function showHideChat() {
    if (statusChatBlock === false) {
        $(".chatW").hide();
        $(".chatWindow").addClass("hideChatWindow");
        $(".chatHead").addClass("hideChatWindow");
        statusChatBlock = true;
    } else {
        $(".chatW").show();
        $(".chatWindow").removeClass("hideChatWindow");
        $(".chatHead").removeClass("hideChatWindow");
        statusChatBlock = false;
    }
}

function ban(id, type) {
    $("#acceptBan").on("click", function () {
        var time = $("input[name=banTime]").val();
        var reason = $("input[name=banReason]").val();
        $.ajax({
            type: "POST",
            url: adr + "/index.php?function=ban",
            data: {id: id, type: type, time: time, reason: reason},
            success: function (data) {
                notify(data);
            }
        });
    });
}


$(function () {

    $("body").tooltip({
        selector: '[data-tip="tooltip"]',
        container: 'body',
        placement: 'auto',
        html: true
    });


    $('[data-toggle="dropdown"]').dropdown();

    var getPage = window.location.href.match(/page=([^&]+)/)[1];

    if (getPage === "chat"){
        startChat();
        setInterval(function () {
            startChat();
            if ($("input[name=autoscroll]").prop('checked')) {
                var div = $(".boxChat");
                div.scrollTop(div.prop('scrollHeight'));
            }
        }, 3000);
    }else{
        if(widgetChat === "on") {
            startChat();
            setInterval(function () {
                startChat();
                if ($("input[name=autoscroll]").prop('checked')) {
                    var div = $("[data-target=chatMessage]");
                    div.scrollTop(div.prop('scrollHeight'));
                }
            }, 3000);
        }
    }

});

var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
    lineNumbers: true,
    styleActiveLine: true,
    matchBrackets: true,
    theme: "material",
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
});
editor.setSize(null, 500);

