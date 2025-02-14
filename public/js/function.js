function getOptionAjax(data, receive, url, setnull, method, func){ //require JQuery Library
	$ = jQuery.noConflict();

	if(setnull!=''){
		$(setnull).children('option[value!=""]').remove();
  }
	var method = (method!=undefined)?method:'POST';

	$.ajax({
            type: method,
            url: url,
            data: data,
            success: function (json_response) {

							//console.log(json_response);

							$(receive).children('option[value!=""]').remove();
							$.each(json_response, function(index, item) {//ลบค่าสร้าง option ใส่ใน select
								$(receive).append('<option value="'+index+'">'+item+'</option>');
							});

							if(func){//process function
								setTimeout(func, 500);
							}
            }
    });
}

function sendRequest(data, url, method, recieve_element, attr, func){//require JQuery Library

	$ = jQuery.noConflict();
	$.ajax({
            type: method,
            url: url,
            data: data,
            success: function (msg) {
				if(attr){
					$(recieve_element).attr(attr, msg);
				}else{
					$(recieve_element).html(msg);
				}
				if(func){setTimeout(func, 500); }
            }, error: function(){
				alert('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้');
			}
    });
}

function CheckRegisterID(element_hidden, element_text){ //require jquery library
	$ = jQuery.noConflict();
	if($(element_hidden).val()=='' && $(element_text).val()!=''){
		alert('ไม่พบข้อมูลนี้ในระบบ..');
		$(element_text).focus();
		return false;
	}
	return true;
}


/****************************** Add By Champ 05/09/2557 ********************/

//ใส่คอมมาให้ตัวเลข
function addCommas(nStr, decimal){
		var tmp='';

		nStr += '';
		x = nStr.split('.');

		if((x.length-1)>=1){
			 if(x[1].length>decimal){//ถ้าหากหลักของทศนิยมเกินที่กำหนดไว้ ตัดให้เหลือเท่าที่กำหนดไว้
				x[1] = x[1].substring(0, decimal);
			 }
			 tmp = '.'+x[1];
		}

		x1 = x[0];
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}

		return x1+tmp;
}

// ลบ คอมมา
function RemoveCommas(nstr){
    return nstr.replace(/[^\d\.\-\ ]/g, '');
}

//เป็น Int หรือไม่
function isInt(n){
	  var n = parseInt(n);
    return Number(n) === n && n % 1 === 0;
}

//เป็น Float หรือไม่
function isFloat(n){
	  var n = parseFloat(n);
    return Number(n) === n && n % 1 !== 0;
}

//ผลต่างวันที่ รูปแบบ วัน/เดือน/ปี(ค.ศ.)
function dateDiff(date1, date2){

	date1 = date1.split("/");
	date2 = date2.split("/");
	sDate = new Date(date1[2],date1[1]-1,date1[0]);
	eDate = new Date(date2[2],date2[1]-1,date2[0]);
	var daysDiff = Math.round((eDate.getTime()-sDate.getTime())/86400000);

	return daysDiff;

}

function dateDiff2(date1, date2){

	date1 = date1.split("/");
	date2 = date2.split("/");
	sDate = new Date(date1[0],date1[1]-1,date1[2]);
	eDate = new Date(date2[0],date2[1]-1,date2[2]);
	var daysDiff = Math.round((eDate.getTime()-sDate.getTime())/86400000);

	return daysDiff;

}

function ValidateFile(Element){
			var MaxSize = jQuery(Element).attr('size');//ขนาดไฟล์ที่อนุญาตให้ อัพโหลด
			var AcceptList = jQuery(Element).attr('accept');//list ประเภทไฟล์ที่อนญาตให้อัพโหลด

			var FileType = jQuery(Element).val();
				  FileType = '.'+FileType.substr(FileType.lastIndexOf('.')+1);
				  FileType = jQuery.trim(FileType.toLowerCase());

			var validate = true;

			if(typeof AcceptList != 'undefined'){//ถ้ามีการกำหนดประเภทไฟล์ที่อนญาตให้อัพโหลด
				AcceptList = AcceptList.split(",");
				validate = false;
                jQuery.each( AcceptList, function( key, accept_type ){
					accept_type = jQuery.trim(accept_type.toLowerCase());
 			 		if(accept_type==FileType){//ถ้าเจอประเภทไฟล์ที่อยู่ใน list
						validate = true;
						return true;
					}
                });
				if(validate==false){
					alert('ประเภทไฟล์ที่คุณเลือกไม่อนุญาตให้อัพโหลด');
				}
			}

			if(typeof MaxSize != 'undefined'){//ถ้ามีการกำหนดขนาดไฟล์สูงสุดเอาไว้
				if(Element.files[0].size>MaxSize){
					alert('ขนาดไฟล์ใหญ่กว่าที่อนุญาต');
					validate=false;
				}
			}

			if(validate==false){
				jQuery(Element).val('');//เคลียร์ input file เป็นค่าว่าง
			}
}

function getAge(o)
 {
    var tmp = o.split("-");
    var d = tmp[2];
    var m = tmp[1];
    var y = tmp[0];
    var nowdt = new Date();
    var nd = parseInt(nowdt.getDate());
    var nm = parseInt(nowdt.getMonth());
    var ny = parseInt(nowdt.getFullYear());
    var age = "";
    var ageYear = 0;
    var ageMonth = 0;
    if(d != "" && m != "" && y != "")
    {
        var s = new Date(y, parseInt(m)-1, d);
        d = parseInt(s.getDate());
        m = parseInt(s.getMonth());
        y = parseInt(s.getFullYear());

        ageYear = ny - y;
        if(nm > m)
        {
            ageMonth = nm-m;
        }else if(nm == m){
            if(nd >= d)
            {
                ageMonth = 0;
            }else{c=2.2;
                ageMonth = 11;
                ageYear = ageYear - 1;
            }
        }else{
            ageMonth = 12-m+nm;
            ageYear = ageYear - 1;
        }
        age = ageYear + " ปี " + ageMonth + " เดือน";
    }else{
        age = "";
    }
		console.log(age);
    //document.getElementById("age").value=age;
 }

 //getAge('2019-06-01');
