var ready = true;

var host = '62.231.168.109';

var scrollFloat = function() {
    'use strict';

    var app = {};

    app.init = function init(node) {
        if (!node || node.nodeType !== 1) {
            throw new Error(node + ' is not DOM element');
        }
        handleWindowScroll(node);
    };

    function handleWindowScroll(floatElement) {
        window.onscroll = function() {
            if (window.scrollY > floatElement.offsetTop) {
                if (floatElement.style.position !== 'fixed') {
                    floatElement.style.position = 'fixed';
                    floatElement.style.top = '0';
                }
            } else {
                if (floatElement.style.position === 'fixed') {
                    floatElement.style.position = '';
                    floatElement.style.top = '';
                }
            }
        };
    }

    return app;
}();

$(document).ready(
    function() {
    	
    	//отступ основного окна сверху на высоту заголовка
		$(".page-region").offset({ top: $(".in_page").height() });
		
		//$("#page-region").style.top;
    	
    	//возвращает цвет в hex
    	$.cssHooks.backgroundColor = {
		    get: function(elem) {
		        if (elem.currentStyle)
		            var bg = elem.currentStyle["backgroundColor"];
		        else if (window.getComputedStyle)
		            var bg = document.defaultView.getComputedStyle(elem,
		                null).getPropertyValue("background-color");
		        if (bg.search("rgb") == -1)
		            return bg;
		        else {
		            bg = bg.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		            function hex(x) {
		                return ("0" + parseInt(x).toString(16)).slice(-2);
		            }
		            return "#" + hex(bg[1]) + hex(bg[2]) + hex(bg[3]);
		            //return hex(bg[1]) + hex(bg[2]) + hex(bg[3]);
		        }
		    }
		};
    	//$('input#color').ColorPicker(options);
// изменение логина и пароля на вход в программу
    	$("#change_pass").live('click', function(event) {
    		if($('input#old_pass').val()=='') {
    			alertify.alert("Введите старый пароль!");
    		} else if(hex_md5($('input#old_pass').val())!=$('input#old_pass_md5').val()) {
    			alertify.alert("Старый пароль не совпадает!");
    			$('input#old_pass').val('');
    		} else if($('input#new_pass').val()=='') {
    			alertify.alert("Введите новый пароль!");
    		} else if($('input#new_pass').val()!=$('input#new_pass2').val()) {
    			alertify.alert("Пароли не воспадают!");
    		} else if(hex_md5($('input#new_pass').val())==$('input#old_pass_md5').val()) {
    			alertify.alert("Новый пароль совпадает со старым.<br>Введите новый пароль!");
    		} else {
	    		alertify.set({ buttonReverse: true, buttonFocus: "cancel"});
	    		alertify.confirm("Изменить пароль?", function (e) {
	    		    if (e) {
	    		    	$.post("./engine/backend.php", {
	    	    			act: 'change_pass',
	    	                user_id: $('input#user_id').val(),
	    	                old_pass: $('input#old_pass').val(),
	    	                new_pass: $('input#new_pass').val()
	    	            }, function (reply) {
	    	                //alert(reply);
	    	            	if(reply=='bad_pass')
	    	            		alert("Введите сложный пароль. Пароль должен содержать цифры, прописные буквы, заглавные буквы и быть не менее 6 символов.");
	    	            	else
	    	            		window.location.reload();
	    	            });
	    		    }
	    		});
    		}
            return false;
        });

//изменение логина пароля на документооборот и на агентс
    	$("#[id^='change_doc_agents_']").live('click', function(event) {
    		var a, b = 0;
    		if(this.id == 'change_doc_agents_d')
    			a = 'Документооборот'
    		else if(this.id == 'change_doc_agents_a') {
    			a = 'Агенты';
	    		b = 1;
			}
    		alertify.set({ buttonReverse: true, buttonFocus: "cancel"});
    		alertify.confirm("Изменить изменить логин/пароль для входа в " + a + "?", function (e) {
    		    if (e) {
    		    	$.post("./engine/backend.php", {
    	    			act: 'change_doc_agents',
    	    			b: b,
    	                doc_user: $('input#doc_user').val(),
    	                doc_pass: $('input#doc_pass').val(),
    	                agents_user: $('input#agents_user').val(),
    	                agents_pass: $('input#agents_pass').val(),
    	            }, function (reply) {
    	                //alert(reply);
    	            	if(reply)
    	            		alert(reply)
    	            	else
    	            		window.location.reload();
    	            });
    		    }
    		});
            return false;
        });

    	$("a#get_uk_info").live('click', function(event) {
    		//alert(name+' '+num);
    		//password_quality_check();
    		$("div#content").html('Загрузка...');
    		$.post("./engine/backend.php", {
    			act: 'get_uk_info',
                //street: $('input#full_addr_name').val(),
                street_id: $('input#street_id').val(),
                house: $('input#full_addr_num').val()
            }, function (reply) {
                //alert(reply);
            	if(reply)
            		$("div#content").html(reply);
            	else
            		$("div#content").html('информация отсутствует');
            });
            return false;
        });

    	$("#[id^='color_']").live('click', function(event) {
    		if($('#color_select').val()!='') return false;
    		var id = $(this).attr('rel_id');
    		var type = $(this).attr('rel_type');
    		var fiber_type = $('#fiber_type').val();
    		$('#color_select').val(id);
    		$.post("./engine/backend.php", {
    			act: 'color_select',
    			id: id,
    			type: type,
    			fiber_type: fiber_type,
    			//street: $('input#full_addr_name').val(),
            }, function (reply) {
                //alert(reply);
                $("[id$='color_"+type+"_"+id+"']").html(reply);
                $(".cities_list").slideToggle('fast');
            	/*if(reply)
            		$("div#content").html(reply);
            	else
            		$("div#content").html('информация отсутствует');*/
            });
            return false;
        });
// применение цвета кабеля
    	$("#cable_fiber_color").live('click', function(event) {
    		var cable_id = $(this).attr('rel_cable_id');
    		alertify.set({ buttonReverse: true, buttonFocus: "cancel"});
    		alertify.confirm("Применить цвета кабеля?",
    			function (e) {
    			if(e) {
    				$.post("./engine/backend.php", {
    					act: 'cable_fiber_color',
    					id: cable_id,
    				}, function (reply) {
    					//alert(reply);
    					if(reply)
                			alert(reply)
                		else
                			window.location.reload();
    				});
	    		}
    		});
            return false;
        });
    	
    	/* выбор города */
    	/*$('.delivery_list').click(function(){
    		$(".cities_list").slideToggle('fast');
    	});*/
    	$('ul.cities_list li').live('click', function(){
    		if(!confirm('Изменить цвет?')) {
    			window.location.reload();
    			return false;
    		}
	    	//var text = $(this).html();
	    	var color_id = $(this).attr('color_id');
	    	var id = $(this).attr('rel_id');
	    	var type = $(this).attr('rel_type');
	    	var fiber_type = $('#fiber_type').val();
	    	//$(".cities_list").slideUp('fast');
	    	//$(".delivery_list span").html(tx);
	    	//$(".delivery_list span").css('backgroundColor', color_id);

	    	//$(".delivery_text").html(tv);
	    	//$('#color_select').val('false');
	    	
	    	$.post("./engine/backend.php", {
    			act: 'set_color',
    			id: id,
    			type: type,
    			color_id: color_id,
    			fiber_type: fiber_type,
    			//street: $('input#full_addr_name').val(),
            }, function (reply) {
            	if(reply)
                    alert(reply);
                else
                	window.location.reload();
            });
    	});

    	$("select#mod_color").live('change', function(event) {
    		//$("select#mod_color").val("ddd");
    		//$("select#mod_color").attr('rel'));
    		//alert($(this).children().find('option[value="'+$(this).val()+'"]').css('backgroundColor'));
    		//alert($(this).children(":selected").attr("id"));
    		var color = $(this).children(":selected").css('backgroundColor');
    		alert(color);
    		$(this).css('backgroundColor', color);
    	});

// показать на карте begin -------------------------------------------------------------------------------------------------------
        //$("button#map").live('click', function(event) {
    	$("#map").live('click', function(event) {
            window.open("http://" + host + "/fibers/geomap.php?"+$(this).attr("rel")+"&zoom=18", '_blank');
            return false;
        });
// end -------------------------------------------------------------------------------------------------------

// показать на карте отслеживание волокон begin -------------------------------------------------------------------------------------------------------
		$("button#[id^='show_fib_map_']").live('click', function(event) {
    		var fiber_id = this.id.replace(/show_fib_map_/, '');
            window.open("http://" + host + "/fibers/geomap.php?"+$(this).attr("rel")+"&zoom=18&find_fiber="+fiber_id, '_blank');
            return false;
        });
// end -------------------------------------------------------------------------------------------------------

// добавление в div begin -------------------------------------------------------------------------------------------------------
// добавление кабеля(cable_add_div), порта(port_add_div) в диве
        $("a#[id$='_into_div']").live('click', function(event) {
            $.post("./engine/backend.php"+$(this).attr("rel"), function (reply) {
            	//alert(reply);
                $("div#action").html(reply);
            });
            $('body,html').animate({scrollTop: 0}, 200);
            if(this.id=='port_add_div') window.location.reload();
            return false;
        });
// добавление в div end -------------------------------------------------------------------------------------------------------

// район begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового района в div
        //$("button#[id$='_area']").live('click', function(event) {
        $("button#n_area,#e_area").live('click', function(event) {
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
                descrip: $('input#descrip').val(),
                region_id: $('select#region_id').val(),
            }, function (reply) {
                //alert(reply.length);
            	// хз, но когда пустота, то кажет что длинна 4
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
// после выбора раона выводит список улиц из агентс 
        	 $("select#area").live('change', function(event) {
        		 get_uk_info_agents_select_area($("select#area").val());
        		 return false;
        	 });
// изменение, внесение новой улицы в div
             $("button#n_street_name,#e_street_name").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
                     street_id: $('select#street_id').val(),
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
             $("button#n_location,#e_location").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
             $("button#n_room,#e_room").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
             $("button#n_key,#e_key").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
             $("button#n_lift_type,#e_lift_type").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
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
             $("button#[id$='_descrip_text']").live('click', function(event) {
                 if($('textarea#descrip_text').val()==0) {
                     alert('Описание пустое');
                     $('textarea#descrip_text').focus();
                     return false;
                 } else if($(this).attr('id')=='d_descrip_text' && !confirm('Удалить описание')) return false; 
                 $.post("./engine/backend.php", {
                     act: $(this).attr('id'),
                     id: $('input#id_descrip_text').val(),
                     text: $('textarea#descrip_text').val(),
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
                	if(reply)
                		alert(reply);
                    //window.location.reload();
                    location.href='?act=s_node';
                });
                return false;
             }
             if(act == 'n_node' && $('select#id').val()==0) {
                 alert('Выберите новый узел');
                 $('select#id').focus();
                 return false;
             } else
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
             var id = $("select#id").val(); 
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
                     descrip: $('input#descrip').val(),
                 }, function (reply) {
                	 if(reply)
                         alert(reply);
                     else {
                    	 var street_n = $("select#street_name option:selected").text().split(/[(]/);
                    	 //alert(street_n[0] + $('input#street_num').val());
                    	 //
                    	 location.href='?act=s_node&find_node=*' + street_n[0] + $('input#street_num').val() + '*';
                    	 //window.location.reload();
                     }
                 });
             return false;
             });
         });
// узел end -------------------------------------------------------------------------------------------------------

// типы коммутаторов begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа коммутатора в div
             $("button#n_switch_type,#e_switch_type").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы коммутаторов end -------------------------------------------------------------------------------------------------------

// коммутаторы begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового коммутатора в div
             $("button#n_switches,#e_switches").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// коммутаторы end -------------------------------------------------------------------------------------------------------

// типы медиаконвертеров begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа медиаконвертера в div
             $("button#n_mc_type,#e_mc_type").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы медиаконвертеров end -------------------------------------------------------------------------------------------------------

// медиаконвертеры begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового медиаконвертера в div
             $("button#n_mc,#e_mc").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
                 }, function (reply) {
                	 if(reply)
                         alert(reply);
                     else
                         window.location.reload();
                 });
                 return false;
             });
// удаление медиаконвертеров
             $("button#d_mc").live('click', function(event) {
                 $.post("./engine/backend.php", {
                     act: 'd_mc',
                     id: $(this).attr("rel"),
                 }, function (reply) {
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// медиаконвертеры end -------------------------------------------------------------------------------------------------------

// типы рам/ящиков begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа рамы/ящика в div
             $("button#n_box_type,#e_box_type").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы рам/ящиков end -------------------------------------------------------------------------------------------------------

// рамы/ящики begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового рам/ящиков в div
             $("button#n_box,#e_box").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// рамы/ящики end -------------------------------------------------------------------------------------------------------

// типы ИБП begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа ИБП в div
             $("button#n_ups_type,#e_ups_type").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы ИБП end -------------------------------------------------------------------------------------------------------

// ИБП begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового ИБП в div
             $("button#n_ups,#e_ups").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// ИБП end -------------------------------------------------------------------------------------------------------

// типы прочего оборудования begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового типа прочего оборудования в div
             $("button#n_other_type,#e_other_type").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// типы прочего оборудования end -------------------------------------------------------------------------------------------------------

// Прочее оборудование begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового прочего оборудования в div
             $("button#n_other,#e_other").live('click', function(event) {
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
                     descrip: $('input#descrip').val(),
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
                	 if(reply)
                		 alert(reply);
                     window.location.reload();
                 });
                 return false;
             });
// Прочее оборудование end -------------------------------------------------------------------------------------------------------


//пользователи begin -------------------------------------------------------------------------------------------------------
// изменение, внесение нового пользователя в div
      $("button#n_user,#e_user").live('click', function(event) {
    	  var id = $("input#id").val(); 
          if(!id) id=0;
          if($('input#login').val()==0) {
              alert('Введите логин');
              $('input#login').focus();
              return false;
          } else if($('input#name').val()==0) {
              alert('Введите имя');
              $('input#name').focus();
              return false;
          } else if($('input#password').val()!=0 && $('input#pass').val()!=0 && !confirm("Изменить пароль?")) {
              return false;
          } else if($('input#password').val()==0 && id==0) {
              alert('Введите пароль');
              $('input#password').focus();
              return false;
          } else if($('input#password2').val()==0 && id==0) {
              alert('Повторите пароль');
              $('input#password2').focus();
              return false;
          } else if($('input#password').val()!=$('input#password2').val()) {
              alert('Пароли должны совпадать!!!');
              return false;
          } else if($('select#group_id').val()==-1) {
        	  alert('Выберите группу');
              $('select#group_id').focus();
              return false;
          } else if($('#status').attr('checked')!='checked' && !confirm("Пользователь не активен, продолжить?")) {
              return false;
          }
          $.post("./engine/backend.php", {
              act: $(this).attr('id'),
              id: id,
              login: $('input#login').val(),
              name: $('input#name').val(),
              password: $('input#password').val(),
              status: $('#status').attr('checked'),
              group: $('select#group_id').val(),
          }, function (reply) {
              if(reply)
                  alert(reply);
              else
                  window.location.reload();
          return false;
          });
      });
// удаление пользователя
/*      $("button#d_user").live('click', function(event) {
          $.post("./engine/backend.php", {
              act: 'd_user',
              id: $(this).attr("rel"),
          }, function (reply) {
              if(reply) alert(reply);
              window.location.reload();
          });
          return false;
      });*/
// пользователи end -------------------------------------------------------------------------------------------------------

// удаление пассивного оборудования
        $("button#d_pq").live('click', function(event) {
            $.post("./engine/backend.php", {
                act: 'd_pq',
                id: $(this).attr("rel"),
            }, function (reply) {
            	if(reply)
            		alert(reply);
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
    		if(!ready) return false;
    		ready = false;
        	if(!confirm("Изменить номер порта?")) {
            	return false;
            }
        	//$("div.page").hide();
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
            	ready = true;
            	if(reply)
                    alert(reply);
                else
                    window.location.reload();
            });
            //return false;
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
            	if(reply)
            		alert(reply);
    	    	window.location.reload();
            });
            return false;
        });

// ввод нового пассивного оборудование / редактирование
    	$("button#new_pq").live('click', function(event) {
    		var reload;
            if($("select#pq_type").val()==0) {
            	alert('Введите тип кросса/муфты');
            	return false;
            }
            //var ports = $("input#pq_ports").val();
            var pq_descrip = $("input#pq_descrip").val();
            //var pq_type = $("select#pq_type").val();
            if($('#prompt').val() && !confirm("Добавил кросс, не забудь удалить неиспользуемые порты!!!"))
            	window.location.reload();
            //if($('#prompt').val()) alertify.alert('Добавил кросс, не забудь удалить неиспользуемые порты!!!');
            $.post("./engine/backend.php", {
                act: $('#act').val()+'_sql',
                id: $('#id').val(),
                node: $("input#node").val(),
                type: $("select#type").val(),
                num: $('#num').val(),
                //ports: ports,
                pq_descrip: pq_descrip,
                pq_type: $("select#pq_type").val(),
            }, function (reply) {
            	//alert(reply);
            	if(reply=="exist")
            		alert('Такой кросс существует');
            	else
            	{
            		//if(!$('#prompt').val())
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

// чекбокс проблеммы на узле
    	$("input#[id^='incorrect_']").live('click', function(event) {
    		var node_id = this.id.replace(/incorrect_/, '');
            var checkbox = $("input#incorrect_"+node_id);
            var incorrect=0;
            if(checkbox.attr('checked')) incorrect=1;

            alertify.set({ buttonReverse: true, buttonFocus: "cancel"});
    		alertify.confirm("Изменить статус 'Проблемма'?", function (e) {
    		    if (e) {
    	            $.post("./engine/backend.php", {
    	            	act: 'incorrect_edit',
    	            	node_id: node_id,
    	                incorrect: incorrect,
    	            }, function (reply) {
    	            	//alert(reply);
    	            	if(reply=='0') checkbox.removeAttr('checked')
    	            	else if(reply=='1') checkbox.attr('checked', 'checked');
    	    	    	//window.location.reload();
    	            });
    		    }
    		});
    		/*return false;
    		if(confirm("Изменить статус 'Проблемма'?")) {
	            var node_id = this.id.replace(/incorrect_/, '');
	            var checkbox = $("input#incorrect_"+node_id);
	            var incorrect=0;
	            if(checkbox.attr('checked')) incorrect=1;
	            $.post("./engine/backend.php", {
	            	act: 'incorrect_edit',
	            	node_id: node_id,
	                incorrect: incorrect,
	            }, function (reply) {
	            	//alert(reply);
	            	if(reply=='0') checkbox.removeAttr('checked')
	            	else if(reply=='1') checkbox.attr('checked', 'checked');
	    	    	//window.location.reload();
	            });
        	}*/
            return false;
        });
    	
// чекбокс в стадии строительства узла
    	$("input#[id^='u_const_']").live('click', function(event) {
    		var node_id = this.id.replace(/u_const_/, '');
            var checkbox = $("input#u_const_"+node_id);
            var u_const=0;
            if(checkbox.attr('checked')) u_const=1;

            alertify.set({ buttonReverse: true, buttonFocus: "cancel"});
    		alertify.confirm("Изменить статус 'В стадии строительства'?", function (e) {
    		    if (e) {
    	            $.post("./engine/backend.php", {
    	            	act: 'u_const_edit',
    	            	node_id: node_id,
    	            	u_const: u_const,
    	            }, function (reply) {
    	            	//alert(reply);
    	            	if(reply=='0') checkbox.removeAttr('checked')
    	            	else if(reply=='1') checkbox.attr('checked', 'checked');
    	    	    	//window.location.reload();
    	            });
    		    }
    		});
            return false;
        });

// кнопка изменения состояния в списке "Узлы в строительстве"
    	$("button#[id^='u_const_']").live('click', function(event) {
    		var node_id = this.id.replace(/u_const_/, '');
    		
            alertify.set({ buttonReverse: true, buttonFocus: "cancel"});
    		alertify.confirm("Изменить статус 'В стадии строительства'?", function (e) {
    		    if (e) {
    	            $.post("./engine/backend.php", {
    	            	act: 'u_const_edit',
    	            	node_id: node_id,
    	            	u_const: 0,
    	            }, function (reply) {
    	            	//alert(reply);
    	            	/*if(reply=='0') checkbox.removeAttr('checked')
    	            	else if(reply=='1') checkbox.attr('checked', 'checked');*/
    	    	    	window.location.reload();
    	            });
    		    }
    		});
            return false;
        });

// ввод/изменение описание порта
    	$("button#[id^='p_descrip_b_']").live('click', function(event) {
        	if(confirm("Изменить описание?")) {
	            var port_id = this.id.replace(/p_descrip_b_/, '');
	            var port_descrip = $("input#p_descrip_"+port_id).val();
	            $.post("./engine/backend.php", {
	            	act: 'port_descrip_edit',
	            	pq_id: $("input#pq_id").val(),
	                port_id: port_id,
	                port_descrip: port_descrip,
	            }, function (reply) {
	            	if(reply)
	            		alert(reply);
	    	    	window.location.reload();
	            });
        	}
            return false;
        });
// удаление порта
        $("button#[id^='p_descrip_d_']").click(function () {
            var port_id = this.id.replace(/p_descrip_d_/, '');
            if($("input#p_descrip_"+port_id).val().length != 0) {
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
            	if(reply=='reload') {
            		window.location.reload();
            		return false;
            	}
                $("div#action").html(reply);
            });
            //$('body,html').animate({scrollTop: 0}, 200);
            return false;
        });

// изменение, внесение и прочее в диве action
        $("button#[id$='in_div']").live('click', function(event) {
        	//alert('sss');
            if($(this).attr('id')!='pq_del_in_div' || confirm("Удалить тип пассивного оборудования?"))
            //if($(this).attr('id')!='cable_del_in_div' || confirm("Удалить тип кабеля?"))
            if($(this).attr('id')!='color_del_in_div' || confirm("Удалить цвет?"))
            //alert($(this).attr("rel"));
            $.post("./engine/backend.php"+$(this).attr("rel"), function (reply) {
            	//alert(reply);
            	if(reply=='reload') {
            		window.location.reload();
            		return false;
            	}
            	if(reply)
            		$("div#action").html(reply);
            });
            //$('body,html').animate({scrollTop: 0}, 200);
            //if($(this).attr('id')=='pq_del_in_div' || $(this).attr('id')=='cable_del_in_div' || $(this).attr('id')=='color_del_in_div') {
            if($(this).attr('id')=='pq_del_in_div' || $(this).attr('id')=='color_del_in_div') {
        		//window.location.reload();
            	//location.href="#";
        		alert('Готово!!!!');
        		window.location.reload();
            };
            return false;
        });

// удаление кабеля
        $("button#[id$='d_cable']").live('click', function(event) {
        	//alert('sss');
            //if($(this).attr('id')!='cable_del_in_div' || confirm("Удалить тип кабеля?"))
            //alert($(this).attr("rel"));
        	var rel = $(this).attr("rel");
        	alertify.set({ buttonReverse: true, buttonFocus: "cancel"});
    		alertify.confirm("Удалить тип кабеля?", function (e) {
    		    if (e) {
    		    	$.post("./engine/backend.php"+rel, function (reply) {
    	            	//alert(reply);
    	            	if(reply=='reload'){
    	            		window.location.reload();
    	            		return false;
    		    		}
    	            	if(reply=='error'){
    	            		alertify.error('Удалить невозможно, кабель используется!!!');
    	            		return;
    	            	}
    	            	if(reply)
    	            		$("div#action").html(reply);
    	            });
    		    }
    		});
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
                descrip: $('input#descrip').val(),
            }, function (reply) {
                //alert(reply);
            	if(reply)
                    alert(reply);
                else
                    window.location.reload();
            return false;
            });
        });

// изменение, внесение и прочее в диве action post цвета
        $("button#[id$='_color']").live('click', function(event) {
            if($('input#name').val()==0) {
                alert('Введите наименование');
                $('input#name').focus();
                return false;
            } else
            if($('input#color').val().length==0) {
                alert('Введите цвет');
                $('input#color').focus();
                return false;
            }
            var id = $("input#id").val(); 
            if(!id) id=0;
            $.post("./engine/backend.php", {
                act: $(this).attr('id'),
                id: id,
                type: $('input#type').val(),
                name: $('input#name').val(),
                color: $('input#color').val(),
                stroke: $('#stroke').attr('checked'),
                descrip: $('input#descrip').val(),
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
            //$('body,html').animate({scrollTop: 0}, 200);
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
            if($('#prompt').val() && !confirm("Добавил кабель, не забудь применить (указать) цвета кабеля!!!"))
            	window.location.reload();

            var descrip = $('#descrip').val();
            $.post("./engine/backend.php", {
                act: $('#act').val()+'_sql',
                id: $('#id').val(),
                pq_1: $('input#pq_1').val(),
                pq_2: $("select#pq_2").val(),
                //fib: $('#fib').val(),
                cable_type: $('select#cable_type').val(),
                descrip: descrip,
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

// изменение типа кабеля div
    	$("button#change_ct").live('click', function(event) {
            var cable_id = $(this).attr('rel_cable_id');
            $.post("./engine/backend.php", {
                act: 'change_cable_type',
                cable_id: cable_id,
            }, function (reply) {
            	//alert(reply);
            	$("div#action").html(reply);
        		/*if(reply)
        			alert(reply);
        		else
        			window.location.reload();*/
        	return false;
            });
        });

// изменение типа кабеля sql
    	$("button#change_ct_sql").live('click', function(event) {
            var cable_id = $(this).attr('rel_cable_id');
            $.post("./engine/backend.php", {
                act: 'change_cable_type_sql',
                cable_id: cable_id,
                cable_type: $('select#cable_type').val(),
            }, function (reply) {
            	//alert(reply);
        		if(reply)
        			alert(reply);
        		else
        			window.location.reload();
        	return false;
            });
        });

// вывод свободных волокон после выбора кабеля 
    	$("select#cable").live('change', function(event) {
			fiber_list_free($("select#cable").val());
			return false;
		});

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
			if(!ready) return false;
			ready = false;
			if(!confirm("Внести изменения?")) {
				window.location.reload();
			}
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
	        	ready = true;
	        	if(reply)
	                alert(reply);
	            else
	                window.location.reload();
	        });
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
            cable_list(pq_id,0,fiber_id,true);
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
        	fiber_list(node_id,pq_id,cable_id,0,fiber_id,port_id);
        });

// изменение статуса плана
        $("select#plan_status").live('change', function(event) {

        	var plan_num = $(this).attr('plan_num');
        	var status = $(this).val();
        	
    		alertify.set({ buttonReverse: true, buttonFocus: "cancel"});
    		alertify.confirm("Изменить статус?", function (e) {
    		    if (e) {
    		    	$.post("./engine/backend.php", {
    	    			act: 'plan_status',
    	                plan_num: plan_num,
    	                status: status
    	            }, function (reply) {
    	                //alert(reply);
    	            	if(reply)
    	            		alert(reply);
    	            	else
    	            		window.location.reload();
    	            });
    		    }
    		});
    	});

        $("select#plan_date").live('change', function(event) {
        	var date = $(this).val();
        	var arr = date.match(/\w+|"[^"]+"/g);
        	alert(arr[0]+' '+arr[1]);
        	window.location.href='?act=plan&month_plan='+arr[0]+'&year_plan='+arr[1];
        	return false;
    	});
        /////// где то здесь закончил
        
        // удаление цвета
/*        $("button#d_color").live('click', function(event) {
        	$.post("./engine/backend.php", {
        		act: 'd_color',
        		id: $(this).attr("rel"),
        	}, function (reply) {
        		if(reply)
        			alert(reply);
        		window.location.reload();
        	});
        	return false;
        });*/
        
// загрузка файлов
        // эмитирование нажатие на кнопку загрузки
        $("button#fake_file_input").live('click', function(event) {
        	$("input[name=file_input]").click();
    		return false;
        });
        
        // действия после выбора файла
        $("input[name=file_input]").live('change', function(event) {
        	$("input#file_input_filename").val($("input[name=file_input]").val());
        	//alert($("input#file_input").val());
    		return false;
        });
        
        var options_upload = { 
        	target:   '#action',   // target element(s) to be updated with server response 
    		beforeSubmit:  beforeSubmit,  // pre-submit callback 
    		success:       afterSuccess,  // post-submit callback 
    		//uploadProgress: OnProgress, //upload progress callback 
    		resetForm: true        // reset the form after successful submit 
    	};
        
        //$('#MyUploadForm').submit(function() {
        $('form#form_submit').live('submit', function(event) {
        	//alert('ddd');
			$(this).ajaxSubmit(options_upload);
			// always return false to prevent standard browser submit and page navigation 
			return false; 
		});
        //action
// конец загрузки файлов
        
    	// удаление файла
        $("button#pq_file_del").live('click', function(event) {
            $.post("./engine/backend.php", {
                act: 'pq_file_del',
                id: $(this).attr("rel"),
            }, function (reply) {
            	if(reply)
            		alert(reply);
                window.location.reload();
            });
            return false;
        });
        
    }
);

// относится к "загрузка файла"
function afterSuccess()
{
	window.location.reload();
};

//относится к "загрузка файла"
function beforeSubmit(){
	if( !$("input[name=file_input]").val()) //check empty input filed
	{
		//$("#output").html("Are you kidding me?");
		alertify.alert("Ты шутишь, что ли?");
		//window.location.reload();
		return false;
	}

	var fsize = $("input[name=file_input]")[0].files[0].size; //get file size
	var ftype = $("input[name=file_input]")[0].files[0].type; // get file type

	//allow file types 
	switch(ftype)
    {
		case 'application/pdf':
            break;
        default:
            //$("#output").html("<b>"+ftype+"</b> Unsupported file type!");
        	alertify.alert("<b>"+ftype+"</b> Несовместимый тип файла!");
			return false
    }
	
	//Allowed file size is less than 5 MB (1048576)
	if(fsize>5242880) 
	{
		//$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big file! <br />File is too big, it should be less than 5 MB.");
		alert("<b>"+bytesToSize(fsize) +"</b> Слишком большой файл! <br />Файл не должен превышать 5 MB.");
		return false
	}
};
// функции

//функция вывода кабелей в кроссе/муфте
function cable_list(pq_id,cable_id,fiber_id,enable) {
	if(!cable_id) cable_id=0;
	$.post("./engine/backend.php", {
        act: 's_cable_list',
        pq_id: pq_id,
        cable_id: cable_id,
        fiber_id: fiber_id,
        enable: enable
    }, function (reply) {
    	//alert(reply);
    	$("select#cable_id_"+fiber_id).html(reply);
    });
	return false;
}

//функция вывода волокон в кабеле
function fiber_list(node_id,pq_id,cable_id,to_fiber_id,fiber_id,port_id) {
	if(!cable_id) return;
	if(cable_id==0) enable=false; else enable=true;
	if(!to_fiber_id) to_fiber_id=0;
	$.post("./engine/backend.php", {
      act: 's_fiber_list',
      node_id: node_id,
      pq_id: pq_id,
      cable_id: cable_id,
      to_fiber_id: to_fiber_id,
      fiber_id: fiber_id,
      port_id: port_id,
      enable: enable
  }, function (reply) {
  	$("select#fiber_id_"+fiber_id).html(reply);
  	// если не выбран кабель, то дизеблим выбор волокон
  	$("select#fiber_id_"+fiber_id).attr("disabled",!enable);
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
	//alert(node_id+' | '+type+' | '+type_id);
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

function color_init() {
	$('#colorpickerHolder').ColorPicker({flat: true});
	/*$('#color').ColorPicker({
		onSubmit: function(hsb, hex, rgb) {
			$('#color').val(hex);
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});*/
	$('#color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
}

var hexcase=0;function hex_md5(a){return rstr2hex(rstr_md5(str2rstr_utf8(a)))}function hex_hmac_md5(a,b){return rstr2hex(rstr_hmac_md5(str2rstr_utf8(a),str2rstr_utf8(b)))}function md5_vm_test(){return hex_md5("abc").toLowerCase()=="900150983cd24fb0d6963f7d28e17f72"}function rstr_md5(a){return binl2rstr(binl_md5(rstr2binl(a),a.length*8))}function rstr_hmac_md5(c,f){var e=rstr2binl(c);if(e.length>16){e=binl_md5(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binl_md5(a.concat(rstr2binl(f)),512+f.length*8);return binl2rstr(binl_md5(d.concat(g),512+128))}function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}function rstr2binl(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(c%32)}return a}function binl2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(c%32))&255)}return a}function binl_md5(p,k){p[k>>5]|=128<<((k)%32);p[(((k+64)>>>9)<<4)+14]=k;var o=1732584193;var n=-271733879;var m=-1732584194;var l=271733878;for(var g=0;g<p.length;g+=16){var j=o;var h=n;var f=m;var e=l;o=md5_ff(o,n,m,l,p[g+0],7,-680876936);l=md5_ff(l,o,n,m,p[g+1],12,-389564586);m=md5_ff(m,l,o,n,p[g+2],17,606105819);n=md5_ff(n,m,l,o,p[g+3],22,-1044525330);o=md5_ff(o,n,m,l,p[g+4],7,-176418897);l=md5_ff(l,o,n,m,p[g+5],12,1200080426);m=md5_ff(m,l,o,n,p[g+6],17,-1473231341);n=md5_ff(n,m,l,o,p[g+7],22,-45705983);o=md5_ff(o,n,m,l,p[g+8],7,1770035416);l=md5_ff(l,o,n,m,p[g+9],12,-1958414417);m=md5_ff(m,l,o,n,p[g+10],17,-42063);n=md5_ff(n,m,l,o,p[g+11],22,-1990404162);o=md5_ff(o,n,m,l,p[g+12],7,1804603682);l=md5_ff(l,o,n,m,p[g+13],12,-40341101);m=md5_ff(m,l,o,n,p[g+14],17,-1502002290);n=md5_ff(n,m,l,o,p[g+15],22,1236535329);o=md5_gg(o,n,m,l,p[g+1],5,-165796510);l=md5_gg(l,o,n,m,p[g+6],9,-1069501632);m=md5_gg(m,l,o,n,p[g+11],14,643717713);n=md5_gg(n,m,l,o,p[g+0],20,-373897302);o=md5_gg(o,n,m,l,p[g+5],5,-701558691);l=md5_gg(l,o,n,m,p[g+10],9,38016083);m=md5_gg(m,l,o,n,p[g+15],14,-660478335);n=md5_gg(n,m,l,o,p[g+4],20,-405537848);o=md5_gg(o,n,m,l,p[g+9],5,568446438);l=md5_gg(l,o,n,m,p[g+14],9,-1019803690);m=md5_gg(m,l,o,n,p[g+3],14,-187363961);n=md5_gg(n,m,l,o,p[g+8],20,1163531501);o=md5_gg(o,n,m,l,p[g+13],5,-1444681467);l=md5_gg(l,o,n,m,p[g+2],9,-51403784);m=md5_gg(m,l,o,n,p[g+7],14,1735328473);n=md5_gg(n,m,l,o,p[g+12],20,-1926607734);o=md5_hh(o,n,m,l,p[g+5],4,-378558);l=md5_hh(l,o,n,m,p[g+8],11,-2022574463);m=md5_hh(m,l,o,n,p[g+11],16,1839030562);n=md5_hh(n,m,l,o,p[g+14],23,-35309556);o=md5_hh(o,n,m,l,p[g+1],4,-1530992060);l=md5_hh(l,o,n,m,p[g+4],11,1272893353);m=md5_hh(m,l,o,n,p[g+7],16,-155497632);n=md5_hh(n,m,l,o,p[g+10],23,-1094730640);o=md5_hh(o,n,m,l,p[g+13],4,681279174);l=md5_hh(l,o,n,m,p[g+0],11,-358537222);m=md5_hh(m,l,o,n,p[g+3],16,-722521979);n=md5_hh(n,m,l,o,p[g+6],23,76029189);o=md5_hh(o,n,m,l,p[g+9],4,-640364487);l=md5_hh(l,o,n,m,p[g+12],11,-421815835);m=md5_hh(m,l,o,n,p[g+15],16,530742520);n=md5_hh(n,m,l,o,p[g+2],23,-995338651);o=md5_ii(o,n,m,l,p[g+0],6,-198630844);l=md5_ii(l,o,n,m,p[g+7],10,1126891415);m=md5_ii(m,l,o,n,p[g+14],15,-1416354905);n=md5_ii(n,m,l,o,p[g+5],21,-57434055);o=md5_ii(o,n,m,l,p[g+12],6,1700485571);l=md5_ii(l,o,n,m,p[g+3],10,-1894986606);m=md5_ii(m,l,o,n,p[g+10],15,-1051523);n=md5_ii(n,m,l,o,p[g+1],21,-2054922799);o=md5_ii(o,n,m,l,p[g+8],6,1873313359);l=md5_ii(l,o,n,m,p[g+15],10,-30611744);m=md5_ii(m,l,o,n,p[g+6],15,-1560198380);n=md5_ii(n,m,l,o,p[g+13],21,1309151649);o=md5_ii(o,n,m,l,p[g+4],6,-145523070);l=md5_ii(l,o,n,m,p[g+11],10,-1120210379);m=md5_ii(m,l,o,n,p[g+2],15,718787259);n=md5_ii(n,m,l,o,p[g+9],21,-343485551);o=safe_add(o,j);n=safe_add(n,h);m=safe_add(m,f);l=safe_add(l,e)}return Array(o,n,m,l)}function md5_cmn(h,e,d,c,g,f){return safe_add(bit_rol(safe_add(safe_add(e,h),safe_add(c,f)),g),d)}function md5_ff(g,f,k,j,e,i,h){return md5_cmn((f&k)|((~f)&j),g,f,e,i,h)}function md5_gg(g,f,k,j,e,i,h){return md5_cmn((f&j)|(k&(~j)),g,f,e,i,h)}function md5_hh(g,f,k,j,e,i,h){return md5_cmn(f^k^j,g,f,e,i,h)}function md5_ii(g,f,k,j,e,i,h){return md5_cmn(k^(f|(~j)),g,f,e,i,h)}function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};
