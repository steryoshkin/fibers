$(document).ready(

    function() {

// добавление в div begin -------------------------------------------------------------------------------------------------------
// добавление кабеля(cable_add_div), порта(port_add_div) в диве
        $("a#[id$='_into_div']").live('click', function(event) {
            $.post("./engine/backend.php"+$(this).attr("rel"), function (reply) {
                $("div#action").html(reply);
            });
            $('body,html').animate({scrollTop: 0}, 200);
            if(this.id=='port_add_div') window.location.reload();
            return false;
        });
// добавление в div end -------------------------------------------------------------------------------------------------------

// район begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового района в div
        $("button#[id$='_area']").live('click', function(event) {
            if($('input#name').val()==0) {
                alert('Введите район');
                $('input#name').focus();
                return false;
            } else
            var id = $("input#id").val(); 
            if(!id) id=0;
            $.post("./engine/backend.php", {
                act: $(this).attr('id'),
                id: id,
                name: $('input#name').val(),
                desc: $('input#desc').val(),
            }, function (reply) {
                //alert(reply);
                if(reply)
                    alert(reply);
                else
                    window.location.reload();
            return false;
            });
        });
// удаление района
        $("button#d_area").live('click', function(event) {
            $.post("./engine/backend.php", {
                act: 'd_area',
                id: $(this).attr("rel"),
            }, function (reply) {
                if(reply) alert(reply);
                window.location.reload();
            });
            return false;
        });
// район end -------------------------------------------------------------------------------------------------------

// улица begin -------------------------------------------------------------------------------------------------------
// изменение, внесение новой улицы в div
             $("button#[id$='_street_name']").live('click', function(event) {
                 if($('input#name').val()==0) {
                     alert('Введите улицу');
                     $('input#name').focus();
                     return false;
                 } else if($('select#area').val()==0) {
                     alert('Введите район');
                     $('input#area').focus();
                     return false;
                 } else
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     name: $('input#name').val(),
                     small_name: $('input#small_name').val(),
                     area_id: $('select#area').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     //alert(reply);
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление улицы
             $("button#d_street_name").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_street_name',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// улица end -------------------------------------------------------------------------------------------------------

// размещение begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового размещения в div
             $("button#[id$='_location']").live('click', function(event) {
                 if($('input#location').val()==0) {
                     alert('Введите размещение');
                     $('input#location').focus();
                     return false;
                 } else
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     location: $('input#location').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     //alert(reply);
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление размещения
             $("button#d_location").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_location',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// размещение end -------------------------------------------------------------------------------------------------------

// помещение begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового помещение в div
             $("button#[id$='_room']").live('click', function(event) {
                 if($('input#room').val()==0) {
                     alert('Введите помещение');
                     $('input#room').focus();
                     return false;
                 } else
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     room: $('input#room').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     //alert(reply);
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление помещение
             $("button#d_room").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_room',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// помещение end -------------------------------------------------------------------------------------------------------

// ключи begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового ключа в div
             $("button#[id$='_key']").live('click', function(event) {
                 if($('input#num').val()==0) {
                     alert('Введите номер ключа');
                     $('input#num').focus();
                     return false;
                 } else
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     num: $('input#num').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление ключа
             $("button#d_key").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_key',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// ключи end -------------------------------------------------------------------------------------------------------

// ключи в узлы begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового, удаление ключи в узлы в div
             $("button#[id$='_key_node']").live('click', function(event) {
                 if($('select#key_node').val()==0) {
                     alert('Введите номер ключа');
                     $('select#key_node').focus();
                     return false;
                 }
                 var id = $("input#node_id").val(); 
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     num: $('select#key_node').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// ключи в узлы end -------------------------------------------------------------------------------------------------------

// лифтёрки begin -------------------------------------------------------------------------------------------------------
// изменение, внесение новых лифтёров в div
             $("button#[id$='_lift_type']").live('click', function(event) {
                 if($('input#name').val()==0) {
                     alert('Введите имя');
                     $('input#name').focus();
                     return false;
                 } else if($('input#tel').val()==0) {
                     alert('Введите телефон');
                     $('input#tel').focus();
                     return false;
                 } else
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     name: $('input#name').val(),
                     tel: $('input#tel').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     //alert(reply);
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление лифтёрок
             $("button#d_lift_type").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_lift_type',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// лифтёрки end -------------------------------------------------------------------------------------------------------

// лифтёрки в узлы begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового, удаление лифтёрки в узлы в div
             $("button#[id$='_lift_node']").live('click', function(event) {
                 if($('select#lift_node').val()==0) {
                     alert('Введите номер лифтёрки');
                     $('select#lift_node').focus();
                     return false;
                 }
                 var id = $("input#node_id").val(); 
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     lift: $('select#lift_node').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// лифтёрки в узлы end -------------------------------------------------------------------------------------------------------

// описание begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового описания в div
             $("button#[id$='_desc_text']").live('click', function(event) {
                 if($('textarea#desc_text').val()==0) {
                     alert('Описание пустое');
                     $('textarea#desc_text').focus();
                     return false;
                 } else if($(this).attr('id')=='d_desc_text' && !confirm('Удалить описание')) return false; 
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: $('input#id_desc_text').val(),
                     text: $('textarea#desc_text').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// описание end -------------------------------------------------------------------------------------------------------

// узел begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового узла в div
         $("button#[id$='_node']").live('click', function(event) {
             var act = $(this).attr('id');
             // удаление узла
             if(act=='d_node') {
                $.post("./engine/backend.php", {
                    act: 'd_node',
                    id: $(this).attr("rel"),
                }, function (reply) {
                    if(reply) alert(reply);
                    window.location.reload();
                });
                return false;
             }
             if($('select#street_name').val()==0) {
                 alert('Выберите улицу');
                 $('select#street_name').focus();
                 return false;
             } else
             if($('input#street_num').val().length == 0) {
                 alert('Введите номер дома');
                 $('input#street_num').focus();
                 return false;
             }
             var id = $("input#id").val(); 
             if(!id) id=0;
             $.post("./engine/backend.php", {
                 act: 'check_street_num',
                 street_name_id: $('select#street_name').val(),
                 street_num: $('input#street_num').val(),
             }, function (reply) {
                 var street_num_id = reply;
                 if(!reply) if(!confirm("Такого номера дома не существует.\nСоздать?")) return false;
                 $.post("./engine/backend.php", {
                     act: act,
                     id: id,
                     street_name_id: $('select#street_name').val(),
                     street_num: $('input#street_num').val(),
                     street_num_id: street_num_id,
                     num_ent: $('input#num_ent').val(),
                     location_id: $('select#location').val(),
                     room_id: $('select#room').val(),
                     incorrect: $('#incorrect').attr('checked'),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 });
             return false;
             });
         });
// узел end -------------------------------------------------------------------------------------------------------

// типы коммутаторов begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа коммутатора в div
             $("button#[id$='_switch_type']").live('click', function(event) {
                 if($('input#name').val()==0) {
                     alert('Введите название');
                     $('input#name').focus();
                     return false;
                 } else if($('input#ports_num').val()==0) {
                     alert('Введите количество портов');
                     $('input#ports_num').focus();
                     return false;
                 }
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     name: $('input#name').val(),
                     ports_num: $('input#ports_num').val(),
                     unit: $('input#unit').val(),
                     power: $('input#power').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление типа коммутатора
             $("button#d_switch_type").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_switch_type',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы коммутаторов end -------------------------------------------------------------------------------------------------------

// коммутаторы begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового коммутатора в div
             $("button#[id$='_switches']").live('click', function(event) {
                 //alert(parseInt($('input#used_ports').val()) +' '+ parseInt($('input#ports_num').val()));
                 if($('select#switch_type_id').attr('id') && $('select#switch_type_id').val()==0) {
                    alert('Выберите тип коммутатора');
                    $('select#switch_type_id').focus();
                    return false;
                 }
                 if(parseInt($('input#used_ports').val()) > parseInt($('input#ports_num').val())) {
                     alert('Неверное количество занятых портов');
                     $('input#used_ports').focus();
                     return false;
                 } else if(parseInt($('input#used_ports').val()) == parseInt($('input#ports_num').val())) {
                     alert('Внимание!!!\nСвободных портов не осталось');
                 }
                 var id = $("input#id").val();
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     node_id: $('input#node_id').val(),
                     switch_type_id: $('select#switch_type_id').val(),
                     used_ports: $('input#used_ports').val(),
                     sn: $('input#sn').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление коммутатора
             $("button#d_switches").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_switches',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// коммутаторы end -------------------------------------------------------------------------------------------------------

// типы медиаконвертеров begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа медиаконвертера в div
             $("button#[id$='_mc_type']").live('click', function(event) {
                 if($('input#name').val()==0) {
                     alert('Введите название');
                     $('input#name').focus();
                     return false;
                 }
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     name: $('input#name').val(),
                     power: $('input#power').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление типа медиаконвертера
             $("button#d_mc_type").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_mc_type',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы медиаконвертеров end -------------------------------------------------------------------------------------------------------

// медиаконвертеры begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового медиаконвертера в div
             $("button#[id$='_mc']").live('click', function(event) {
                 if($('select#mc_type_id').attr('id') && $('select#mc_type_id').val()==0) {
                    alert('Выберите тип медиаконвертера');
                    $('select#mc_type_id').focus();
                    return fals;
                 }
                 var id = $("input#id").val();
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     node_id: $('input#node_id').val(),
                     mc_type_id: $('select#mc_type_id').val(),
                     sn: $('input#sn').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление медиаконвертеров
             $("button#d_mc").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_mc',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// медиаконвертеры end -------------------------------------------------------------------------------------------------------

// типы рам/ящиков begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа рамы/ящика в div
             $("button#[id$='_box_type']").live('click', function(event) {
                 if($('input#name').val()==0) {
                     alert('Введите название');
                     $('input#name').focus();
                     return false;
                 }
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     name: $('input#name').val(),
                     unit: $('input#unit').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление типа рамы/ящика
             $("button#d_box_type").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_box_type',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы рам/ящиков end -------------------------------------------------------------------------------------------------------

// рамы/ящики begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового рам/ящиков в div
             $("button#[id$='_box']").live('click', function(event) {
                 if($('select#box_type_id').attr('id') && $('select#box_type_id').val()==0) {
                    alert('Выберите тип рамы/ящика');
                    $('select#box_type_id').focus();
                    return fals;
                 }
                 var id = $("input#id").val();
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     node_id: $('input#node_id').val(),
                     box_type_id: $('select#box_type_id').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление рамы/ящика
             $("button#d_box").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_box',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// рамы/ящики end -------------------------------------------------------------------------------------------------------

// типы ИБП begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа ИБП в div
             $("button#[id$='_ups_type']").live('click', function(event) {
                 if($('input#name').val()==0) {
                     alert('Введите название');
                     $('input#name').focus();
                     return false;
                 }
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     name: $('input#name').val(),
                     unit: $('input#unit').val(),
                     power: $('input#power').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление типа ИБП
             $("button#d_ups_type").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_ups_type',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы ИБП end -------------------------------------------------------------------------------------------------------

// ИБП begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового ИБП в div
             $("button#[id$='_ups']").live('click', function(event) {
                 if($('select#ups_type_id').attr('id') && $('select#ups_type_id').val()==0) {
                    alert('Выберите тип ИБП');
                    $('select#ups_type_id').focus();
                    return fals;
                 }
                 var id = $("input#id").val();
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     node_id: $('input#node_id').val(),
                     ups_type_id: $('select#ups_type_id').val(),
                     sn: $('input#sn').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление ИБП
             $("button#d_ups").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_ups',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// ИБП end -------------------------------------------------------------------------------------------------------

// типы прочего оборудования begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа прочего оборудования в div
             $("button#[id$='_other_type']").live('click', function(event) {
                 if($('input#name').val()==0) {
                     alert('Введите название');
                     $('input#name').focus();
                     return false;
                 }
                 var id = $("input#id").val(); 
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     name: $('input#name').val(),
                     unit: $('input#unit').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление типа прочего оборудования
             $("button#d_other_type").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_other_type',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы прочего оборудования end -------------------------------------------------------------------------------------------------------

// Прочее оборудование begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового прочего оборудования в div
             $("button#[id$='_other']").live('click', function(event) {
                 if($('select#other_type_id').attr('id') && $('select#other_type_id').val()==0) {
                    alert('Выберите тип прочего оборудования');
                    $('select#other_type_id').focus();
                    return fals;
                 }
                 var id = $("input#id").val();
                 if(!id) id=0;
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: id,
                     node_id: $('input#node_id').val(),
                     other_type_id: $('select#other_type_id').val(),
                     desc: $('input#desc').val(),
                 }, function (reply) {
                     if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 return false;
                 });
             });
// удаление прочее оборудование
             $("button#d_other").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_other',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                     if(reply) alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// Прочее оборудование end -------------------------------------------------------------------------------------------------------

// удаление пассивного оборудования
        $("button#d_pq").live('click', function(event) {
            $.post("./engine/backend.php", {
                act: 'd_pq',
                id: $(this).attr("rel"),
            }, function (reply) {
                if(reply) alert(reply);
                window.location.reload();
            });
            return false;
        });

// после выбора узла выводит список добавления возможного пассивного оборудования 
    	$("select#node").live('change', function(event) {
        	var node_id = $("select#node").val();
        	var type_id = $("input#type_id").val();
        	s_pq_type_ports(node_id,0,type_id);
    	});

// вывод свободных портов после выбора кросса 
    	$("select#pq_id").live('change', function(event) {
			ports_list_free($("select#pq_id").val());
			return false;
		});

// после выбора в списке оборудования кросса вывести поле ввода количества портов 
    	//$("select#type").live('change', function(event) {
    	$("select#type").live('change', function(event) {
    		//.live('click', function(event) {
        	if($("select#type").val()==0)
        		$("div#ports").show();
        	else
        		$("div#ports").hide();
        	
	    	return false;
    	});

//показать все кабеля
        $("button#[id^='pq_all_cable_']").live('click', function(event) {
            var node_id = this.id.replace(/pq_all_cable_/, '');
            $.post("./engine/backend.php", {
                act: 'pq_all_cable',
                node_id: node_id,
            }, function (reply) {
                //if(reply) alert(reply);
                //window.location.reload();
                $("div#pq_all_cable").html(reply);
            });
            return false;
        });

// записывать в базу порт при выборе порта для присоединения с волокном
    	$("select#[id^='ports_']").live('change', function(event) {
        	if(!confirm("Изменить номер порта?")) {
            	return false;
            }
        	
        	$("div.page").hide();
            var fiber_id = this.id.replace(/ports_/, '');
        	//var pq_id = $("select#pq_id_"+fiber_id).val();
        	var pq_id = $("input#pq_id").val();
        	var port_id = $("select#ports_"+fiber_id).val();
        	var curr_port_id = $("input#curr_port_id_"+fiber_id).val();
        	//alert('port_id: '+port_id+' curr_port_id: '+curr_port_id);
            $.post("./engine/backend.php", {
            	act: 'fiber_port_conn',
                pq_id: pq_id,
                port_id: port_id,
                fiber_id: fiber_id,
                curr_port_id: curr_port_id,
            }, function (reply) {
            	if(reply) alert(reply);
    	    	window.location.reload();
            });
            return false;
        });

// удаление кабеля с волокнами
    	$("button#d_cable_all_button").live('click', function(event) {
        	if(!confirm("Удалить кабель с волокнами?")) {
            	return false;
            }
        	var cable_id = $("input#cable_id").val();
            $.post("./engine/backend.php", {
            	act: 'd_cable_all',
            	cable_id: cable_id,
            }, function (reply) {
            	if(reply) alert(reply);
    	    	window.location.reload();
            });
            return false;
        });

// ввод нового пассивного оборудование / редактирование
    	$("button#new_pq").live('click', function(event) {
            if($('select#node').val()==0) {
            	alert('Введите узел');
            	return false;
            }
            //var ports = $("input#pq_ports").val();
            var pq_desc = $("input#pq_desc").val();
            //var pq_type = $("select#pq_type").val();
            $.post("./engine/backend.php", {
                act: $('#act').val()+'_sql',
                id: $('#id').val(),
                node: $("input#node").val(),
                //type: $("select#type").val(),
                num: $('#num').val(),
                //ports: ports,
                pq_desc: pq_desc,
                pq_type: $("select#pq_type").val(),
            }, function (reply) {
            	//alert(reply);
            	if(reply=="exist")
            		alert('Такой узел существует');
            	else
            	{
                	window.location.reload();
            	}
        	return false;
            });
        });

// чекбокс занятости порта
    	$("input#[id^='port_used_']").live('click', function(event) {
        	if(confirm("Изменить занятость порта?")) {
	            var port_id = this.id.replace(/port_used_/, '');
	            var checkbox = $("input#port_used_"+port_id);
	            var port_used=0;
	            if(checkbox.attr('checked')) port_used=1;
	            $.post("./engine/backend.php", {
	            	act: 'port_used_edit',
	            	pq_id: $("input#pq_id").val(),
	                port_id: port_id,
	                port_used: port_used,
	            }, function (reply) {
	            	//alert(reply);
	            	if(reply=='0') checkbox.removeAttr('checked')
	            	else if(reply=='1') checkbox.attr('checked', 'checked');
	    	    	//window.location.reload();
	            });
        	}
            return false;
        });

// ввод/изменение описание порта
    	$("button#[id^='p_desc_b_']").live('click', function(event) {
        	if(confirm("Изменить описание?")) {
	            var port_id = this.id.replace(/p_desc_b_/, '');
	            var port_desc = $("input#p_desc_"+port_id).val();
	            $.post("./engine/backend.php", {
	            	act: 'port_desc_edit',
	            	pq_id: $("input#pq_id").val(),
	                port_id: port_id,
	                port_desc: port_desc,
	            }, function (reply) {
	            	if(reply) alert(reply);
	    	    	window.location.reload();
	            });
        	}
            return false;
        });
// удаление порта
        $("button#[id^='p_desc_d_']").click(function () {
            var port_id = this.id.replace(/p_desc_d_/, '');
            if($("input#p_desc_"+port_id).val().length != 0) {
                if(!confirm("Описание порта не пустое!\nУдалить порт?!")) {
                    return false;
                }
            } else
            if(!confirm("Удалить порт?")) {
                return false;
            }
            $.post("./engine/backend.php", {
                act: 'd_port',
                port_id: port_id,
            }, function (reply) {
                //if(reply) alert(reply);
                window.location.reload();
            });
            return false;
        });

// добавление кабеля(cable_add_div), порта(port_add_div) в диве
        $("button#[id$='_add_div']").live('click', function(event) {
            $.post("./engine/backend.php"+$(this).attr("rel"), function (reply) {
                $("div#action").html(reply);
            });
            $('body,html').animate({scrollTop: 0}, 200);
            if(this.id=='port_add_div') window.location.reload();
            return false;
        });

// изменение, внесение и прочее в диве action
        $("button#[id$='in_div']").live('click', function(event) {
            if($(this).attr('id')!='pq_del_in_div' || confirm("Удалить тип пассивного оборудования?"))
            if($(this).attr('id')!='cable_del_in_div' || confirm("Удалить тип кабеля?"))
            $.post("./engine/backend.php"+$(this).attr("rel"), function (reply) {
                $("div#action").html(reply);
            });
            $('body,html').animate({scrollTop: 0}, 200);
            if($(this).attr('id')=='pq_del_in_div' || $(this).attr('id')=='cable_del_in_div') window.location.reload();
            return false;
        });

// изменение, внесение и прочее в диве action post пассивное оборудование
        $("button#[id$='_pq_type']").live('click', function(event) {
            if($('input#name').val()==0) {
                alert('Введите наименование');
                return false;
            } else
            if($('select#type').val()==0 && $('input#ports_num').val().length==0) {
                alert('Введите количество портов');
                return false;
            }
            var id = $("input#id").val(); 
            if(!id) id=0;
            $.post("./engine/backend.php", {
                act: $(this).attr('id'),
                id: id,
                type: $('select#type').val(),
                name: $('input#name').val(),
                ports_num: $('input#ports_num').val(),
                unit: $('input#unit').val(),
            }, function (reply) {
                //alert(reply);
                if(reply)
                    alert(reply);
                else
                    window.location.reload();
            return false;
            });
        });

// изменение, внесение и прочее в диве action post кабели
        $("button#[id$='_cable_type']").live('click', function(event) {
            if($('input#name').val()==0) {
                alert('Введите наименование');
                $('input#name').focus();
                return false;
            } else
            if($('input#fib').val().length==0) {
                alert('Введите количество волокон');
                $('input#fib').focus();
                return false;
            }
            var id = $("input#id").val(); 
            if(!id) id=0;
            $.post("./engine/backend.php", {
                act: $(this).attr('id'),
                id: id,
                name: $('input#name').val(),
                fib: $('input#fib').val(),
                desc: $('input#desc').val(),
            }, function (reply) {
                //alert(reply);
                if(reply)
                    alert(reply);
                else
                    window.location.reload();
            return false;
            });
        });

// действие при выборе кросса или муфты в селекте при внесении или изменении нового пассивного оборудования 
        $("select#type").live('change', function(event) {
            if($("select#type").val()==0) {
                $("div#ports_num_div").show();
                $("div#unit_div").show();
            } else if($("select#type").val()==1) {
                $("div#ports_num_div").hide();
                $("div#unit_div").hide();
            }
            s_pq_type_sel($("select#type").val());
            return false;
        });

// редактирование (перемещение кабеля) move_cable_div и редактирование волокон кабеля edit_cable_div
        $("button#[id$='_cable_div']").live('click', function(event) {
            $.post("./engine/backend.php"+$(this).attr("rel"), function (reply) {
                $("div#action").html(reply);
            });
            $('body,html').animate({scrollTop: 0}, 200);
            return false;
        });

// кнопка отмены (рефреш страници)
        $("button#exit").live('click', function(event) {
            window.location.reload();
            return false;
        });

// ввод нового кабеля
    	$("button#new_cable").live('click', function(event) {
            if($('input#pq_1').val().length == 0) {
            	alert('Ашипка, ничо не работает... pq_1 не задан почему-то');
            	return false;
            }
            if($('select#pq_2').val()==0) {
            	alert('Введите конечную кросс/муфту');
            	$('select#pq_2').focus();
            	return false;
            }
            /*if($('select#pq_1').val()==$('select#pq_2').val()) {
            	alert('Начальная и конечная кросс/муфта не должны совпадать!');
            	return false;
            }*/
            var pq_id = '';

// после ввода нового кабеля, переходит на список кабелей данного кросса/муфты 
            //if($("select#pq_1").val()!='') pq_id='&pq_id='+$("select#pq_1").val();
            var desc = $('#desc').val();
            $.post("./engine/backend.php", {
                act: $('#act').val()+'_sql',
                id: $('#id').val(),
                pq_1: $('input#pq_1').val(),
                pq_2: $("select#pq_2").val(),
                //fib: $('#fib').val(),
                cable_type: $('select#cable_type').val(),
                desc: desc,
            }, function (reply) {
            	//alert(reply);
            	if(reply=="exist")
            		alert('Такой узел существует');
            	else
            	{
            		if(reply)
            			alert(reply);
            		else
            			window.location.reload();
            	}
        	return false;
            });
        });

// вывод свободных волокон после выбора кабеля 
    	$("select#cable").live('change', function(event) {
			fiber_list_free($("select#cable").val());
			return false;
		});

// ввод нового волокна в кабеле
/*    	$("input#new_fiber").live('click', function(event) {
    		alert($("input#cable_id").val());
    		return false;
            if($('select#fiber').val()==0) {
            	alert('Введите волокно');
            	return false;
            }
            $.post("./engine/backend.php", {
                act: $('#act').val()+'_sql',
                cable_id: $("input#cable_id").val(),
                num: $("select#fiber").val(),
            }, function (reply) {
            	//alert(reply);
            	if(reply=="exist")
            		alert('Такой узел существует');
            	else
            	{
                	window.location="?act=s_fiber&cable_id="+$("select#cable").val();
            	}
        	return false;
            });
        });
*/
// ввод нового волокна в кабеле
    	$("input#new_fiber_").live('click', function(event) {
            if($('select#cable').val()==0) {
            	alert('Введите кабель');
            	return false;
            }
            if($('select#fiber').val()==0) {
            	alert('Введите волокно');
            	return false;
            }
            $.post("./engine/backend.php", {
                act: $('#act').val()+'_sql',
                cable_id: $("select#cable").val(),
                num: $("select#fiber").val(),
            }, function (reply) {
            	//alert(reply);
            	if(reply=="exist")
            		alert('Такой узел существует');
            	else
            	{
                	window.location="?act=s_fiber&cable_id="+$("select#cable").val();
            	}
        	return false;
            });
        });

// ввод нового порта в кроссе
    	$("input#new_port").live('click', function(event) {
            /*if($('select#pq_id').val()==0) {
            	alert('Введите кросс');
            	return false;
            }
            if($('select#fiber').val()==0) {
            	alert('Введите порт');
            	return false;
            }*/
            $.post("./engine/backend.php", {
                act: 'n_port',
                pq_id: $("input#pq_id").val(),
                port: $("select#ports").val(),
            }, function (reply) {
            	if(reply=="exist")
            		alert('Такой узел существует');
            	else
            	{
	    			window.location.reload();
            	}
        	return false;
            });
        });

// удаление волокна
    	$("input#[id^='d_fiber_']").live('click', function(event) {
    		var fiber_id = this.id.replace(/d_fiber_/, '');
    		if(!fiber_id) {
    			alert('Корявое чота не работает');
    			return false;
    		}
    		if(confirm("Удалить волокно №"+fiber_id)) {
    			//alert("норм");
    			$.post("./engine/backend.php", {
        	        act: 'd_fiber',
        	        fiber_id: fiber_id,
        	    }, function (reply) {
        	    	if(reply) alert(reply);
	    			window.location.reload();
        	    });
    		}
    		return false;
    	});

// ввод нового соединение волокон
		$("button#[id^='new_fib_conn_']").live('click', function(event) {
			if(confirm("Внести изменения?")) {
				var node_id = $("input#node_id").val();
				var pq_id = $("input#pq_id").val();
	    		var fiber_id = this.id.replace(/new_fib_conn_/, '');
	    		var cable_id = $("select#cable_id_"+fiber_id).val();
	    		var to_fiber_id = $("select#fiber_id_"+fiber_id).val();
	    		//alert("fiber_id: "+fiber_id+" cable_id: "+" to_fiber_id: "+to_fiber_id);
	    		if(cable_id==0) {
	    			alert('Введите кабель');
	    			return false;
	    		}
	    		$.post("./engine/backend.php", {
	    	        act: 'n_fiber_conn',
	    	        fiber_id: fiber_id,
	    	        to_fiber_id: to_fiber_id,
	    	        node_id: node_id,
	    	    }, function (reply) {
	    	    	//alert(reply);
	    	    	window.location="?act=s_cable&pq_id="+pq_id;
	    	    	//alert("?act=s_cable&pq_id="+pq);
	    	    });
			} else {
				window.location.reload();
			}
    		//return false;
    	});

// ввод нового соединения волокон при выборе волокна
		$("select#[id^='fiber_id_']").live('change', function(event) {
			var fiber_id = this.id.replace(/fiber_id_/, '');
			document.getElementById('new_fib_conn_'+fiber_id).click();
    	});

// удаление соединение волокон
		$("button#[id^='del_fib_conn_']").live('click', function(event) {
			var node_id = $("input#node_id").val();
    		var fiber_id = this.id.replace(/del_fib_conn_/, '');
    		var to_fiber_id = $("select#fiber_id_"+fiber_id).val();
    		//alert("node_id: "+node_id+" cable_id: "+fiber_id+" to_fiber_id: "+to_fiber_id);
    		if(!to_fiber_id) {
    			return false;
    		}
    		if(confirm("Удалить соединение волокон "+fiber_id+" - "+to_fiber_id)) {
	    		$.post("./engine/backend.php", {
	    	        act: 'd_fiber_conn',
	    	        node_id: node_id, 
	    	        fiber_id: fiber_id,
	    	        to_fiber_id: to_fiber_id,
	    	    }, function (reply) {
	    	    	if(reply) alert(reply);
	    	    	window.location.reload();
	    	    });
    		}
    		return false;
    	});

// отслеживание соединения волокон
		$("button#[id^='find_fib_conn_']").live('click', function(event) {
			var node_id = $("input#node_id").val();
    		var fiber_id = this.id.replace(/find_fib_conn_/, '');
    		var to_fiber_id = $("select#fiber_id_"+fiber_id).val();
    		// если 0, то будет искать от этого волокна до конца
    		if(!to_fiber_id) to_fiber_id=0;
    		var to_node_id = $("input#to_node_"+fiber_id).val();
    		//var pq_id_iq = $("input#pq_id_"+fiber_id).val();
    		////var pq_id_iq = $("input#node_id").val();
    		//alert("node_id: "+node_id+" cable_id: "+fiber_id+" to_fiber_id: "+to_fiber_id);
    		/*if(!to_fiber_id) {
    			return false;
    		}*/
    		$.post("./engine/backend.php", {
    	        act: 'f_fiber_conn',
    	        node_id: node_id, 
    	        fiber_id: fiber_id,
    	        to_fiber_id: to_fiber_id,
    	        //pq_id_iq: pq_id_iq,
    	        to_node_id: to_node_id,
    	    }, function (reply) {
    	    	//if(reply) alert(reply);
    	    	$("div#f_fiber_"+fiber_id).html(reply);
    	    	$("button#find_fib_conn_"+fiber_id).hide();
                $("button#f_fiber_clean_"+fiber_id).show();

    	    	//$("div#f_fiber_"+fiber_id).show();
    	    	$("tr#f_fiber_tr_"+fiber_id).show();
    	    	//$("div#f_fiber_"+fiber_id).width($(window).width()-60);
    	    	//window.location.reload();
    	    });
    		return false;
    	});
// очистить вывод отслеживание соединения волокон
        $("button#[id^='f_fiber_clean_']").live('click', function(event) {
            var fiber_id = this.id.replace(/f_fiber_clean_/, '');
            //$("div#f_fiber_"+fiber_id).html("");
            $("tr#f_fiber_tr_"+fiber_id).hide();
            $("button#find_fib_conn_"+fiber_id).show();
            $("button#f_fiber_clean_"+fiber_id).hide();
            //$("div#f_fiber_"+fiber_id).hide();
            return false;
        });

// отслеживание занятости портов
		$("button#[id^='find_fib_used_']").live('click', function(event) {
			var node_id = $("input#node_id").val();
			var pq_id = $("input#pq_id").val();
    		var fiber_id = this.id.replace(/find_fib_used_/, '');
    		var to_fiber_id = $("select#fiber_id_"+fiber_id).val();
    		// если 0, то будет искать от этого волокна до конца
    		if(!to_fiber_id) to_fiber_id=0;
    		var to_node_id = $("input#to_node_"+fiber_id).val();
    		//var pq_id_iq = $("input#pq_id_"+fiber_id).val();
    		////var pq_id_iq = $("input#node_id").val();
    		//alert("node_id: "+node_id+" cable_id: "+fiber_id+" to_fiber_id: "+to_fiber_id);
    		/*if(!to_fiber_id) {
    			return false;
    		}*/
    		$.post("./engine/backend.php", {
    	        act: 'f_fiber_used',
    	        node_id: node_id, 
    	        fiber_id: fiber_id,
    	        to_fiber_id: to_fiber_id,
    	        //pq_id_iq: pq_id_iq,
    	        pq_id: pq_id,
    	        to_node_id: to_node_id,
    	    }, function (reply) {
    	    	//if(reply) alert(reply);
    	    	$("div#f_fiber_"+fiber_id).html(reply);
    	    	//$("button#find_fib_conn_"+fiber_id).hide();
                //$("button#f_fiber_clean_"+fiber_id).show();

    	    	//$("div#f_fiber_"+fiber_id).show();
    	    	$("tr#f_fiber_tr_"+fiber_id).show();
    	    	//$("div#f_fiber_"+fiber_id).width($(window).width()-60);
    	    	//window.location.reload();
    	    });
    		return false;
    	});

// очистить вывод отслеживание соединения волокон
/*        $("button#[id^='f_fiber_clean_']").live('click', function(event) {
            var fiber_id = this.id.replace(/f_fiber_clean_/, '');
            //$("div#f_fiber_"+fiber_id).html("");
            $("tr#f_fiber_tr_"+fiber_id).hide();
            $("button#find_fib_conn_"+fiber_id).show();
            $("button#f_fiber_clean_"+fiber_id).hide();
            //$("div#f_fiber_"+fiber_id).hide();
            return false;
        });*/
// вывод списка кабелей в кроссе при смене кросса 
        $("select#[id^='pq_id_']").live('change', function(event) {
            var fiber_id = this.id.replace(/pq_id_/, '');
            //node_id = $("input#node_id").val();
            //pq_id = $("input#pq_id").val(); // - не правильно было
            pq_id = $("select#pq_id_"+fiber_id).val();
            ////alert("fiber_id "+fiber_id+" pq_id "+pq_id);
            //pq_type = $("input#pq_type").val();
            //pq_num = $("input#pq_num").val();
            //alert('pq_id: ' + pq_id + ' node_id:' + node_id + " " + fiber_id);
            //cable_list($("select#pq_from_" + s).val(),s,0);
            //pq_list(node_id,pq_id,pq_type,pq_num,fiber_id);
            //pq_list(node_id,pq_id,pq_type,pq_num,fiber_id);
            //cable_list(pq_id,cable_id,fiber_id)
            cable_list(pq_id,0,fiber_id);
            $("select#fiber_id_"+fiber_id).html("");
            $("select#fiber_id_"+fiber_id).attr("disabled",true);
        });

//вывод списка волокон в кабеле при смене кабеля 
        $("select#[id^='cable_id_']").live('change', function(event) {
        	var fiber_id = this.id.replace(/cable_id_/, '');
        	var port_id = $("select#ports_"+fiber_id).val();
        	var node_id = $("input#node_id").val();
        	var cable_id = $("select#cable_id_"+fiber_id).val();
        	var pq_id = $("select#pq_id_"+fiber_id).val();
        	if(!pq_id) pq_id = $("input#pq_id_"+fiber_id).val();
        	//alert("fiber: "+fiber_id+" cable: "+cable_id+" pq: "+pq_id+" node:"+node_id);
        	//fiber_list(pq_id,cable_id,to_fiber_id,fiber_id);
        	//alert('node_id: '+node_id+' pq_id: '+pq_id+' cable_id: '+cable_id+' fiber_id: '+fiber_id);
        	fiber_list(node_id,pq_id,cable_id,0,fiber_id,true,port_id);
        	//fiber_list(pq_id,cable_id,0,fiber_id);
        	//fiber_list($("select#pq_from_" + cable_id).val(), $("select#to_cable_" + cable_id).val(),cable_id,0);
        });
        /////// где то здесь закончил

    }
);

// функции
// функция вывода свободных волокон после выбора кабеля
function pq_list(node_id,pq_id,pq_type,pq_num,fiber_id,dis) {
    // если номер кросса/муфты не задан, установим значение 0
    if(!pq_num) pq_num=0;
    //alert(pq_id);
    $.post("./engine/backend.php", {
        act: 's_pq_list',
        node_id: node_id,
        pq_id: pq_id,
        pq_type: pq_type,
        pq_num: pq_num,
        fiber_id: fiber_id,
    }, function (reply) {
        $("select#pq_id_" + fiber_id).html(reply);
        //alert(reply);
        //if($("select#pq_id_"+fiber_id+" option").length < 2) $("select#fiber_id_"+fiber_id).attr("disabled",true);
        //if($("select#pq_id_"+fiber_id+" option").length > 1) $("select#pq_id_"+fiber_id).attr("disabled",false);
        if($("select#pq_id_"+fiber_id+" option").length > 1) $("select#pq_id_"+fiber_id).attr("disabled",!dis);
    });
}

//функция вывода кабелей в кроссе/муфте
function cable_list(pq_id,cable_id,fiber_id) {
	//alert('111');
	if(!cable_id) cable_id=0;
	$.post("./engine/backend.php", {
        act: 's_cable_list',
        pq_id: pq_id,
        cable_id: cable_id,
        fiber_id: fiber_id,
    }, function (reply) {
    	//alert(reply);
    	$("select#cable_id_"+fiber_id).html(reply);
    	//$("select#fiber_id_"+fiber_id).html("<select class=\"fiber\" id=\"fiber_id_".fiber_id."\"></select>");
    });
}

//функция вывода волокон в кабеле
function fiber_list(node_id,pq_id,cable_id,to_fiber_id,fiber_id,dis,port_id) {
	//alert(port_id);
	if(!cable_id) return;
	if(!to_fiber_id) to_fiber_id=0;
	//alert("node_id: "+node_id+" pq_id: "+pq_id+ " cable_id: "+cable_id+" to_fiber_id: "+to_fiber_id+" fiber_id: "+fiber_id);
	$.post("./engine/backend.php", {
      act: 's_fiber_list',
      node_id: node_id,
      pq_id: pq_id,
      cable_id: cable_id,
      to_fiber_id: to_fiber_id,
      fiber_id: fiber_id,
      port_id: port_id,
  }, function (reply) {
	/*if(fiber_id==2958) {
		alert(port_id);
		alert(reply);
	}*/
  	//alert(reply+"\nвывод списка волокон при смене кабеля функция");
  	$("select#fiber_id_"+fiber_id).html(reply);
  	// если не выбран кабель, то дизеблим выбор волокон
  	if(cable_id==0) disabled=true; else disabled=false;
  	$("select#fiber_id_"+fiber_id).attr("disabled",!dis);
  });
}

// функция вывода свободных волокон после выбора кабеля
function fiber_list_free(cable) {
	$.post("./engine/backend.php", {
        act: 's_fiber_free',
        id: cable,
    }, function (reply) {
    	//alert(reply);
    	$("select#fiber").html(reply);
    });
}

// вывод добавления количества портов при выборе типа пассивного оборудования кросса
function s_pq_type_ports(node_id,type,type_id) {
	$.post("./engine/backend.php", {
	    act: 's_pq_type',
	    node_id: node_id,
	    type: type,
	    type_id: type_id,
	}, function (reply) {
		//alert(reply);
		eval(reply)
		$("select#type").html(select_type);
		s_pq_type_sel($("select#type").val(),type_id);
		if(show) $("div#ports").show(); else $("div#ports").hide();
	});
}

function s_pq_type_sel(type,type_id) {
	//alert(type+' '+type_id);
	$.post("./engine/backend.php", {
	    act: 's_pq_type_sel',
	    type: type,
	    type_id: type_id,
	}, function (reply) {
		//alert(reply);
		$("select#pq_type").html(reply);
		//if(show) $("div#ports").show(); else $("div#ports").hide();
	});
}

// функция выбора соединения порта с волокном
function fiber_ports(pq_id,fiber_id,dis) {
	//alert("pq "+pq_id);
	$.post("./engine/backend.php", {
	    act: 's_fiber_ports',
	    pq_id: pq_id,
	    fiber_id: fiber_id,
	}, function (reply) {
		//alert(reply);
		eval(reply);
		$("select#ports_"+fiber_id).attr("disabled",dis);
		$("select#ports_"+fiber_id).html(select_ports);
		$("input#curr_port_id_"+fiber_id).val(eval('curr_port_'+fiber_id));
		//if($("select#pq_id_"+fiber_id+" option").length > 1) $("select#pq_id_"+fiber_id).attr("disabled",false);
		//curr_port_id_
/*		eval(reply)
		$("select#type").html(select_type);
		if(show) $("input#pq_ports").show();*/
	});
}

//функция вывода свободных портов после выбора кросса
function ports_list_free(pq_id,pq_type_id) {
	$.post("./engine/backend.php", {
        act: 's_port_free',
        pq_id: pq_id,
        pq_type_id: pq_type_id,
    }, function (reply) {
    	//alert(reply);
    	$("select#ports").html(reply);
    });
}
