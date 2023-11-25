function checkForEnter(event){console.log(this);if(event.keyCode==13){currentBoxNumber=textboxes.index(this);if(textboxes[currentBoxNumber+1]!=null){nextBox=textboxes[currentBoxNumber+1]
  nextBox.focus();event.preventDefault();return false;}}}
  function valid(o,w){o.value=o.value.replace(r[w],'');}
  function formatNumber(input)
  {var num=input.value.replace(/\,/g,'');if(!isNaN(num)){if(num.indexOf('.')>-1){num=num.split('.');num[0]=num[0].toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1,').split('').reverse().join('').replace(/^[\,]/,'');if(num[1].length>2){alert('You may only enter two decimals!');num[1]=num[1].substring(0,num[1].length-1);}input.value=num[0]+'.'+num[1];}else{input.value=num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1,').split('').reverse().join('').replace(/^[\,]/,'')};}
  else{alert('Anda hanya diperbolehkan memasukkan angka!');input.value=input.value.substring(0,input.value.length-1);}}
  function cleardiv(Hasil){document.getElementById(Hasil).innerHTML="";}
  function dialoghapus(Url,Hasil){if(window.confirm("Anda yakin ingin menghapus data ini?")==true){load(Url,Hasil);}}
  function load(page,div){var image_load="<div class='ajax_loading'><img src='"+loading_image_large+"' /></div>";$.ajax({url:site+page,beforeSend:function(){$(div).html(image_load);},success:function(response){$(div).html(response);},error:function(xhr,ajaxOptions,thrownError){$(div).html(xhr.status+" - - "+thrownError);},dataType:"html"});return false;}
  function send_form_no_response(formObj,action)
  {var image_load="<div class='ajax_loading'><img src='"+loading_image_large+"' /></div>";$.ajax({url:site+action,data:$(formObj.elements).serialize(),type:"post",dataType:"html"});return false;}
  function send_form_loading(formObj,action,responseDIV)
  {var image_load="<div class='ajax_loading'><img src='"+loading_image_large+"' /></div>";$.ajax({url:site+action,data:$(formObj.elements).serialize(),beforeSend:function(){$(responseDIV).html(image_load);},success:function(response){$(responseDIV).html(response);},type:"post",dataType:"html"});return false;}
  function load_small(page,div,loadingDom,opt){var image_load_small="<span class='ajax_loading_small'><img src='"+loading_image_small+"' /></span>";$.ajax({url:site+"/"+page,beforeSend:function(){$(loadingDom).html(image_load_small);},success:function(response){$(loadingDom).html('');if(opt=="append")
  {$(div).append(response);}
  else
  {$(div).html(response);}},dataType:"html"});return false;}
  function load_no_loading(page,div){$.ajax({url:page,success:function(response){$(div).html(response);},dataType:"html"});return false;}
  function dummyload(page){$.ajax({url:site+"/"+page,dataType:"html"});return false;}
  function send_form(formObj,action,responseDIV)
  {$.ajax({url:site+"/"+action,data:$(formObj.elements).serialize(),success:function(response){$(responseDIV).html(response);},type:"post",dataType:"html"});return false;}