var el = document.querySelectorAll('#homepage-generator')[0];
el.innerHTML = '';

var strVar="";
strVar += "<style>";
strVar += "    #random-container{";
strVar += "        border: 1px solid #ccf;";
strVar += "        width: 158px;";
strVar += "        \/*height: 154px;*\/";
strVar += "        font-size: 9pt;";
strVar += "        font-family: verdana, sans;";
strVar += "    }";
strVar += "";
strVar += "    #header-random{";
strVar += "        background: #ccccff;";
strVar += "        text-align: center;";
strVar += "        padding: 2px 0;";
strVar += "        color: #00000c;";
strVar += "    }";
strVar += "";
strVar += "    #body-random{";
strVar += "        padding: 10px 5px 5px 5px;";
strVar += "    }";
strVar += "";
strVar += "    .text-form-random{";
strVar += "        width: 40px;";
strVar += "        display: inline-block;";
strVar += "        color: #77777d;";
strVar += "    }";
strVar += "";
strVar += "    .row-random{";
strVar += "        margin-bottom: 5px;";
strVar += "    }";
strVar += "";
strVar += "    .row-random > input{";
strVar += "        width: 80px;";
strVar += "    }";
strVar += "";
strVar += "    #result-random{";
strVar += "        color: #777777;";
strVar += "    }";
strVar += "";
strVar += "    #line-bottom-random{";
strVar += "        height: 4px;";
strVar += "        \/*width: 100%;*\/";
strVar += "        background: #ccccff;";
strVar += "    }";
strVar += "";
strVar += "    #copyright-random{";
strVar += "        color: #797b7a;";
strVar += "        font-size: 8px;";
strVar += "        padding-top: 10px;";
strVar += "        text-align: right;";
strVar += "    }";
strVar += "";
strVar += "    #copyright-random a{";
strVar += "        color: #797b7a;";
strVar += "        text-decoration: underline!important;";
strVar += "    }";
strVar += "";
strVar += "    #copyright-random a:hover{";
strVar += "        color: #000;";
strVar += "    }";
strVar += "";
strVar += "    .line-result-random{";
strVar += "        color: #000;";
strVar += "        font-size: 11pt;";
strVar += "        background: #CCCCFF;";
strVar += "        padding: 2px;";
strVar += "        \/* margin-bottom: 10px; *\/";
strVar += "        height: auto!important;";
strVar += "    }";
strVar += "<\/style>";
strVar += "";
strVar += "<div id=\"random-container\">";
strVar += "    <div id=\"header-random\">";
strVar += "        True Random Number Generator";
strVar += "    <\/div>";
strVar += "";
strVar += "    <div id=\"body-random\">";
strVar += "        <div class=\"row-random\">";
strVar += "            <span class=\"text-form-random\">Min: <\/span>";
strVar += "            <input type=\"text\" name=\"min\" id=\"_min\" value=\"1\" \/>";
strVar += "        <\/div>";
strVar += "";
strVar += "        <div class=\"row-random\">";
strVar += "            <span class=\"text-form-random\">Max: <\/span>";
strVar += "            <input type=\"text\" name=\"max\" id=\"_max\" value=\"100\" \/>";
strVar += "        <\/div>";
strVar += "";
strVar += "        <button value=\"\" id=\"_generate\">Generate<\/button>";
strVar += "";
strVar += "        <div id=\"result-random\">Result:<\/div>";
strVar += "        <div id=\"line-bottom-random\"><\/div>";
strVar += "";
strVar += "        <div id=\"copyright-random\">";
strVar += "            Powered by <a href=\"RANDOM.ORG\">RANDOM.ORG<\/a>";
strVar += "        <\/div>";
strVar += "    <\/div>";
strVar += "<\/div>";
strVar += "";
strVar += "";



var node = document.createElement("div");
node.innerHTML = strVar;
el.appendChild(node);

var count = 0;

var list_number = localStorage.getItem('list_number').split(',');

document.getElementById('_generate').addEventListener('click', function(e){
    // alert('ok');

    var min = document.getElementById('_min').value;
    var max = document.getElementById('_max').value;

    if(min == ''){
        min = 1;
        document.getElementById('_min').value = 1;
    }

    if(max == ''){
        max = 100;
        document.getElementById('_max').value = 100;
    }

    if(min >= max){
        max = parseInt(min) + 1;
        document.getElementById('_max').value = max;
    }

    var el_line = document.getElementById('line-bottom-random');

    var className = 'line-result-random';

    if (el_line.classList)
        el_line.classList.add(className);
    else
        el_line.className += ' ' + className;


    el_line.innerHTML = '<img src="https://www.random.org/util/cp/images/ajax-loader.gif" />';

    setTimeout(function () {
        var number = list_number[count];
        el_line.innerHTML = number;
        count++;
        if(count == list_number.length){
            count = 0;
        }

    }, 2000);
});

