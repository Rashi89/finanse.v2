function zegar(){
var data = new Date();
var godziny = data.getHours();
var minuty = data.getMinutes();
var sekundy = data.getSeconds();


if(godziny<10) godziny="0"+godziny;
if(minuty<10) minuty="0"+minuty;
if(sekundy<10) sekundy="0"+sekundy;


document.getElementById("zegar").innerHTML=godziny+":"+minuty+":"+sekundy;
setTimeout("zegar()",1000);
}
window.onload=zegar;