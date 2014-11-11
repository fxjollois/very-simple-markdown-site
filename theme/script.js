var menu = document.getElementsByTagName("nav")[0].getElementsByTagName("ul")[0];
// console.log(menu.children);

for (i=0; i< menu.children.length; i++) {
	li = menu.children[i];
	if (li.children.length > 1) {
		li.addEventListener("mouseover", function(){ this.children[1].style.visibility = "initial"; });
		li.addEventListener("mouseout", function(){ this.children[1].style.visibility = "hidden"; });
//		li.children[1].style.display = "none";
		}
	}
	
