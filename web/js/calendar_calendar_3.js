$(document).ready(function() {

    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();
    var allowRegisterDays = [2,4,5];
    var actionUrl = Routing.generate('get_events');

    var calendar = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title'
        },
        selectable: true,
        selectHelper: true,
        editable: false,
        disableDragging: true,
        disableResizing: true,
        monthNames: ['Січень','Лютий','Березень','Квітень','Травень','Червень','Липень','Серпень','Вересень','Жовтень','Листопад','Грудень'],
        dayNames: ['Неділя','Понеділок','Вівторок','Середа','Четвер','П’ятниця','Субота'],
        dayNamesShort: ['Неділя','Понеділок','Вівторок','Середа','Четвер','П’ятниця','Субота'],
        buttonText: {
            today: 'Сьогодні'
        },
        firstDay: 1,
        eventAfterRender: function(event, element) {

        },
        viewRender: function(currentView) {

        },
        dayRender: function(date, cell) {
            var diffTime;
            var diffDays;
            diffTime = date - new Date();
            diffDays=Math.round(diffTime/1000/60/60/24);

            if (date < new Date() || diffDays > 30) {
                cell.attr('style', 'background-color: #d6d6d6')
            }
            else if (-1 != jQuery.inArray(date.getDay(), allowRegisterDays)) {

            }

        },
        eventClick: function (event, eventElement) {
            if (false == event.busy) {
                $( "#dialog-form").data('dateStart', event.start.toApi());
                $( "#dialog-form").data('dateEnd', event.end.toApi());
                $("#time-to-appointment").text(dateFormat(event.start, 'dd mm yyyy | HH:MM'));
                $( "#dialog-form" ).dialog( "open" );
            }
        },
        events: actionUrl
    });

});

$(function() {
    var name = $( "#name" ),
        email = $( "#email" ),
        phone = $( "#phone" ),
        allFields = $( [] ).add( name ).add( email ).add( phone ),
        tips = $( ".validateTips" );

    function updateTips( t ) {
        tips
            .text( t )
            .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Довжина " + n + " повинна бути не менше " +
                min + " та не більше " + max + " символів." );
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }

    $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 380,
        width: 350,
        modal: true,
        buttons: {
            "Запис на розпис": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );

                bValid = bValid && checkLength( name, "username", 3, 16 );
                bValid = bValid && checkLength( phone, "phone", 9, 11 );

                bValid = bValid && checkRegexp( name, /^([а-яА-я\s])+$/i, "Ім’я повинно складатися тільки із літер" );
                bValid = bValid && checkRegexp( phone, /^([0-9])+$/, "Телефон повинет бути тільки з цифер" );

                if ( bValid ) {
                    $('#validateTips').text("");
                    $.ajax({
                        type: "PUT",
                        url: Routing.generate('put_event'),
                        data: JSON.stringify({
                            start: $('#dialog-form').data('dateStart'),
                            end: $('#dialog-form').data('dateEnd'),
                            title: 'Роспись хной',
                            client_name: $('#name').val(),
                            phone: $('#phone').val()
                        }),
                        dataType: 'json',
                        contentType: 'application/json',
                        error: function(jqXHR, textStatus, errorThrown) {
                            $( "#dialog-form" ).dialog( "close" );
                            alert('Чтото пошло не так :(' + textStatus + '   ' + errorThrown);
                        },
                        success: function(data, textStatus, jqXHR) {
                            if (data.success) {
                                $( "#dialog-form" ).dialog( "close" );
                                $( "#dialog").find('p').text(data.message);
                                $( "#dialog").attr('title', 'Успішно');
                                $( "#dialog" ).dialog();
//                                    alert(data.success);
                            }
                            else if (data.error) {
                                $( "#dialog-form" ).dialog( "close" );
                                $( "#dialog").find('p').text(data.error);
                                $( "#dialog").attr('title', 'Помилка');
                                $( "#dialog" ).dialog();
//                                    alert(data.error);
                            }
                            else {
                                $( "#dialog-form" ).dialog( "close" );
                                alert('Упс! Щось пішло не так :(')
                            }
                        }

                    });
                    allFields.val( "" ).removeClass( "ui-state-error" );
                }
            },
            "Відмінити": function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
            $('#validateTips').text("");
        }
    });
});