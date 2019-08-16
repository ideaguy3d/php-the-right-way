<?php
    setcookie("julius", $_COOKIE["julius"], time() + time() + (86400 * 60), "/", "julius3d.com")
?>
<!doctype html>
<html lang="en">
<head>
    <title>Three29 Test</title>

    <style>
        /* http://meyerweb.com/eric/tools/css/reset/
           v2.0 | 20110126
           License: none (public domain)
        */
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed,
        figure, figcaption, footer, header, hgroup,
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline;
        }

        /* HTML5 display-role reset for older browsers */
        article, aside, details, figcaption, figure,
        footer, header, hgroup, menu, nav, section {
            display: block;
        }

        body {
            line-height: 1;
            font-family: sans-serif;
        }

        ol, ul {
            list-style: none;
        }

        blockquote, q {
            quotes: none;
        }

        blockquote:before, blockquote:after,
        q:before, q:after {
            content: '';
            content: none;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
        }
    </style>

    <style>
        #div1 {
            height: 140px;
            background: #434343 url("http://www.lovethispic.com/uploaded_images/20521-Rocky-Beach-Sunset.jpg") center/cover no-repeat;
        }

        #div2 {
            background-color: orange;
            height: 140px;
        }

        #div2inner {
            width: 80px;
            height: 80px;
            margin: auto;
        }

        #div3 {
            background-color: blue;
            height: 140px;
        }

        #div4 {
            background-color: yellow;
            height: 140px;
        }

        #div4inner {
            text-align: center;
            padding-top: 10px;
        }

        @media screen and (max-width: 600px) {
            #div4 {
                display: none;
            }

            #div3 {
                display: none;

            }
        }

        #div1.w100 {
            width: 100%;
            -webkit-transition: all 0.7s linear;
            -moz-transition: all 0.7s linear;
            -ms-transition: all 0.7s linear;
            -o-transition: all 0.7s linear;
            transition: all 0.7s linear;
        }

        #div1.w25 {
            width: 25%;
            -webkit-transition: all 0.7s linear;
            -moz-transition: all 0.7s linear;
            -ms-transition: all 0.7s linear;
            -o-transition: all 0.7s linear;
            transition: all 0.7s linear;
        }

        #div2.w100 {
            width: 100%;
            -webkit-transition: all 0.3s linear;
            -moz-transition: all 0.3s linear;
            -ms-transition: all 0.3s linear;
            -o-transition: all 0.3s linear;
            transition: all 0.3s linear;
        }

        #div2.w75 {
            width: 75%;
            -webkit-transition: all 0.3s linear;
            -moz-transition: all 0.3s linear;
            -ms-transition: all 0.3s linear;
            -o-transition: all 0.3s linear;
            transition: all 0.3s linear;
        }

        #div3.w100 {
            width: 100%;
            -webkit-transition: all 0.5s linear;
            -moz-transition: all 0.5s linear;
            -ms-transition: all 0.5s linear;
            -o-transition: all 0.5s linear;
            transition: all 0.5s linear;
        }

        #div3.w50 {
            width: 50%;
            -webkit-transition: all 0.5s linear;
            -moz-transition: all 0.5s linear;
            -ms-transition: all 0.5s linear;
            -o-transition: all 0.5s linear;
            transition: all 0.5s linear;
        }

        #div4.w100 {
            width: 100%;
            -webkit-transition: all 0.2s linear;
            -moz-transition: all 0.2s linear;
            -ms-transition: all 0.2s linear;
            -o-transition: all 0.2s linear;
            transition: all 0.2s linear;
        }

        #div4.w90 {
            width: 90%;
            -webkit-transition: all 0.2s linear;
            -moz-transition: all 0.2s linear;
            -ms-transition: all 0.2s linear;
            -o-transition: all 0.2s linear;
            transition: all 0.2s linear;
        }

    </style>
</head>


<body>

<?php //phpinfo(); ?>

<div id="wrapper">
    <div id="div1" class="divitem w25"></div>

    <div id="div2" class="divitem w75">
        <div id="div2inner"></div>
    </div>

    <div id="div3" class="divitem w50"></div>

    <div id="div4" class="divitem w90">
        <div id="div4inner"></div>
    </div>
</div>


<!-- Custom JS -->
<script>

    const div1 = document.getElementById('div1');
    const div2 = document.getElementById('div2');
    const div2inner = document.getElementById('div2inner');
    const div3 = document.getElementById('div3');
    const div4 = document.getElementById('div4');
    const div4inner = document.getElementById('div4inner');

    // add a '|' to easily .explode() in php
    let divStates = ["25|", "75|", "50|", "90|"];

    jInitState();

    function jInitState() {
        // init the numbers
        for (let i = 0; i < 10; i++) {
            if (i % 2 !== 0) div4inner.innerHTML += " " + i + " ";
        }
        div4inner.innerText = div4inner.innerText
            + div4inner.innerText.split(" ").reverse().join(" ");
        div4inner.innerText = div4inner.innerText.replace("99", "9");

        // init state from cookie
        let initReq = new XMLHttpRequest();
        initReq.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                console.log("the initReq = " + this.responseText);
                let strAR = this.responseText.split(" | ");
                console.log(strAR);

                // for some unknown reason "no suggestion" is getting suffixed
                if(strAR[4].indexOf("no suggestion") > -1) {
                    strAR[4] = strAR[4].replace("no suggestion", "");
                }

                // set the random image
                strAR[0] = strAR[0].replace("randomImage=", "");
                div2inner.style.background = '#434343 url(' + strAR[0] + ') center/cover no-repeat';

                // start the width state initialization
                if (strAR[1] === "div1=100") {
                    div1.setAttribute("class", "divitem w100");
                } else if (strAR[1] === "div1=25") {
                    div1.setAttribute("class", "divitem w25");
                }

                if (strAR[2] === "div2=100") {
                    div2.setAttribute("class", "divitem w100");
                } else if (strAR[2] === "div2=75") {
                    div2.setAttribute("class", "divitem w75");
                }

                if (strAR[3] === "div3=100") {
                    div3.setAttribute("class", "divitem w100");
                } else if (strAR[3] === "div3=50") {
                    div3.setAttribute("class", "divitem w50");
                }

                if (strAR[4] === "div4=100") {
                    div4.setAttribute("class", "divitem w100");
                } else if (strAR[4] === "div4=90") {
                    div4.setAttribute("class", "divitem w90");
                }
            }
        };
        initReq.open("GET", "api.php?q=init");
        initReq.send();

        console.log("< jInitState has been invoked >");
    }

    function div1state() {
        // width transition code
        let div1width = div1.getAttribute("class");
        switch (div1width) {
            case "divitem w100":
                div1.setAttribute("class", "divitem w25");
                divStates[0] = "25|";
                break;
            case "divitem w25":
                div1.setAttribute("class", "divitem w100");
                divStates[0] = "100|";
                break;
            default:
                console.error("div1 width - something went wrong :/");
        }

        // maintain the current state of the other divs
        let divitemAR = document.getElementsByClassName("divitem");
        divStates[1] = divitemAR[1].className === "divitem w75" ? "75|" : "100|";
        divStates[2] = divitemAR[2].className === "divitem w50" ? "50|" : "100|";
        divStates[3] = divitemAR[3].className === "divitem w90" ? "90|" : "100|";
        console.log("jha - divStates =");
        console.log(divStates);

        let stateStr = "";
        divStates.forEach(e => stateStr += e);

        // cookie state code
        let jreq = new XMLHttpRequest();
        jreq.onreadystatechange = function() {
            if(this.readyState === 4 && this.status === 200) {
                console.log("jha - The server response =");
                console.log(this.responseText);
            }
        };
        jreq.open("GET", "api.php?q=" + stateStr, true);
        jreq.send();

        console.log("jha - div1 clicked");
        console.log("stateStr = "+stateStr);
    }

    function div2state() {
        // width transition code
        let div2width = div2.getAttribute("class");
        switch (div2width) {
            case "divitem w75":
                div2.setAttribute("class", "divitem w100");
                divStates[1] = "100|";
                break;
            case "divitem w100":
                div2.setAttribute("class", "divitem w75");
                divStates[1] = "75|";
                break;
            default:
                console.error("div2 width - something went wrong :/");
        }

        // maintain the current state of the other divs
        let divitemAR = document.getElementsByClassName("divitem");
        divStates[0] = divitemAR[0].className === "divitem w25" ? "25|" : "100|";
        divStates[2] = divitemAR[2].className === "divitem w50" ? "50|" : "100|";
        divStates[3] = divitemAR[3].className === "divitem w90" ? "90|" : "100|";
        console.log("jha - divStates=");
        console.log(divStates);

        let stateStr = "";
        divStates.forEach(e => stateStr += e);

        // cookie state code
        let jreq = new XMLHttpRequest();
        jreq.onreadystatechange = function() {
            if(this.readyState === 4 && this.status === 200) {
                console.log("jha - The server response =");
                console.log(this.responseText);
            }
        };
        jreq.open("GET", "api.php?q=" + stateStr, true);
        jreq.send();

        console.log("jha - div2 clicked");
        console.log("stateStr = "+stateStr);
    }

    function div3state() {
        // width transition code
        let div3width = div3.getAttribute("class");
        switch (div3width) {
            case "divitem w100":
                div3.setAttribute("class", "divitem w50");
                divStates[2] = "50|";
                break;
            case "divitem w50":
                div3.setAttribute("class", "divitem w100");
                divStates[2] = "100|";
                break;
            default:
                console.error("div3 width - something went wrong :/");
        }

        // maintain the current state of the other divs
        let divitemAR = document.getElementsByClassName("divitem");
        divStates[0] = divitemAR[0].className === "divitem w25" ? "25|" : "100|";
        divStates[1] = divitemAR[1].className === "divitem w75" ? "75|" : "100|";
        divStates[3] = divitemAR[3].className === "divitem w90" ? "90|" : "100|";
        console.log("jha - divStates=");
        console.log(divStates);

        let stateStr = "";
        divStates.forEach(e => stateStr += e);

        // cookie state code
        let jreq = new XMLHttpRequest();
        jreq.onreadystatechange = function() {
            if(this.readyState === 4 && this.status === 200) {
                console.log("jha - The server response =");
                console.log(this.responseText);
            }
        };
        jreq.open("GET", "api.php?q=" + stateStr, true);
        jreq.send();

        console.log("jha - div3 clicked");
        console.log("stateStr = "+stateStr);
    }

    function div4state() {
        // width transition code
        let div4width = div4.getAttribute("class");
        switch (div4width) {
            case "divitem w100":
                div4.setAttribute("class", "divitem w90");
                divStates[3] = "90|";
                break;
            case "divitem w90":
                div4.setAttribute("class", "divitem w100");
                divStates[3] = "100|";
                break;
            default:
                console.error("div4 width - something went wrong :/");
        }

        // maintain the current state of the other divs
        let divitemAR = document.getElementsByClassName("divitem");
        divStates[0] = divitemAR[0].className === "divitem w25" ? "25|" : "100|";
        divStates[1] = divitemAR[1].className === "divitem w75" ? "75|" : "100|";
        divStates[2] = divitemAR[2].className === "divitem w50" ? "50|" : "100|";

        console.log("jha - divStates=");
        console.log(divStates);

        let stateStr = "";
        divStates.forEach(e => stateStr += e);

        // cookie state code
        let jreq = new XMLHttpRequest();
        jreq.onreadystatechange = function() {
            if(this.readyState === 4 && this.status === 200) {
                console.log("jha - The server response =");
                console.log(this.responseText);
            }
        };
        jreq.open("GET", "api.php?q=" + stateStr, true);
        jreq.send();

        console.log("jha - div4 clicked");
        console.log("stateStr = "+stateStr);
    }

    div1.onclick = div1state;
    div2.onclick = div2state;
    div3.onclick = div3state;
    div4.onclick = div4state;

</script>

</body>
</html>