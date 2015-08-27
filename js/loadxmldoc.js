function loadXMLDoc(dname) {

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/bar/foo.txt", true);
    xhr.onload = function (e) {
      if (xhr.readyState === 4) {
        if (xhr.status === 200) {
          console.log(xhr.responseText);
        } else {
          console.error(xhr.statusText);
        }
      }
    };
    xhr.onerror = function (e) {
      console.error(xhr.statusText);
    };
    xhr.send(null);


    if (window.XMLHttpRequest)
      {
      xmlhttp=new XMLHttpRequest();
      }
    else
      {
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }

    xmlhttp.onreadystatechange=function()
      {
      if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
        document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
        }
      }
    xmlhttp.open("GET", dname, true);
    xmlhttp.send();
    // xhttp.open("GET",dname,false);
    // xhttp.send();
    return xmlhttp.responseXML;
}




// function getFileContents(filePath, callbackFn, scope) {
//     var xhr = new XMLHttpRequest();
//     xhr.onreadystatechange = function() {
//         if (xhr.readyState == 4) {
//             callbackFn.call(scope, xhr.responseText);
//         }
//     }
//     xhr.open("GET", chrome.extension.getURL(filePath), true);
//     xhr.send();
// }


// //then to call it:
// var test = "lol";

// getFileContents("hello.js", function(data) {
//     alert(test);
// }, this);


// var xhr = new XMLHttpRequest();
// xhr.open("GET", "/bar/foo.txt", true);
// xhr.onload = function (e) {
//   if (xhr.readyState === 4) {
//     if (xhr.status === 200) {
//       console.log(xhr.responseText);
//     } else {
//       console.error(xhr.statusText);
//     }
//   }
// };
// xhr.onerror = function (e) {
//   console.error(xhr.statusText);
// };
// xhr.send(null);