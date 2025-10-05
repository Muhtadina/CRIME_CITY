var acc = document.getElementsByClassName("ques");
var i;

for(i=0; i<acc.length; i++){
acc[i].addEventListener("click", function
    (){
this.classList.toggle("active");

var ans = this.nextElementSibling;
if(ans.style.display == "block"){
    ans.style.display = "none";
    ans.style.transition = "0.45s ease-out";
}
else{
    ans.style.display = "block";
    ans.style.transition = "0.45s ease-in";
}
});
}