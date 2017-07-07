<style type="text/css">
    /* Eric Myer's Global Reset http://meyerweb.com/eric/tools/css/reset/index.html v1.0 | 20080212 */
    html, body, div, span, applet, object, iframe,
    h1, h2, h3, h4, h5, h6, p, blockquote, pre,
    a, abbr, acronym, address, big, cite, code,
    del, dfn, em, font, img, ins, kbd, q, s, samp,
    small, strike, strong, sub, sup, tt, var,
    b, u, i, center,
    dl, dt, dd, ol, ul, li,
    fieldset, form, label, legend,
    table, caption, tbody, tfoot, thead, tr, th, td {
        margin: 0;
        padding: 0;
        border: 0;
        outline: 0;
        font-size: 100%;
        vertical-align: baseline;
        background: transparent;
    }

    #true-random-integer-generator {
        font-family: verdana, sans;
        font-size: 9pt;
        background: #FFFFFF;
        padding: 5px;
        margin: 0px;
        width: 148px;
        color: #777777;

        border: 1px solid #CCCCFF;}

    #true-random-integer-generator-title {
        text-align: center;
        padding: 1px;
        display: block;
        background: #CCCCFF;
        color: #000000;
        margin: -6px;
        margin-bottom: 10px;
    }


    #true-random-integer-generator-min-span {
        display: block;
        margin-bottom: 5px;
    }

    #true-random-integer-generator-min-span label {
        color: #777777;
    }

    #true-random-integer-generator-min-span input {
        width: 77px;
        margin-left: 10px;
    }

    #true-random-integer-generator-max-span {
        display: block;
        margin-bottom: 5px;
    }

    #true-random-integer-generator-max-span label {
        color: #777777;
    }

    #true-random-integer-generator-max-span input {
        width: 77px;
        margin-left: 6px;
    }

    #true-random-integer-generator-button {
        display: block;
    }

    #true-random-integer-generator-result {
        display: block;
        padding: 2px;
        margin-bottom: 10px;
        color: #000000;
        background:  #CCCCFF;
        font-size: 11pt;
    }

    #true-random-integer-generator-credits {
        display: block;
        font-size: 6pt;
        text-align: right;
        padding: 1px;
    }

    #true-random-integer-generator-credits a {
        color: #777777;
    }

    #true-random-integer-generator-credits a:hover {
        color: #000000;
    }
</style>

<div id="true-random-integer-generator">
    <span id="true-random-integer-generator-title">True Random Number Generator</span>
    <span id="true-random-integer-generator-min-span">
		<label for="true-random-integer-generator-min">Min:</label>
		<input type="text" name="true-random-integer-generator-min" id="true-random-integer-generator-min" maxlength="9" value="1" onkeypress="return integerJsInputControl(event);">
	</span>
    <span id="true-random-integer-generator-max-span">
		<label for="true-random-integer-generator-max">Max:</label>
		<input type="text" name="true-random-integer-generator-max" id="true-random-integer-generator-max" maxlength="9" value="100" onkeypress="return integerJsInputControl(event);">
	</span>
    <span id="true-random-integer-generator-max-button-span">
		<input type="button" value="Generate" name="true-random-integer-generator-button" id="true-random-integer-generator-button">
	</span>
    <label for="true-random-integer-generator-result">Result:</label>
    <span id="true-random-integer-generator-result"></span>
    <span id="true-random-integer-generator-credits">Powered by <a href="https://www.random.org/" target="_blank">RANDOM.ORG</a></span>
</div>