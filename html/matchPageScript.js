 $(document).ready(function () {

	'use strict';

	var tinderContainer = document.querySelector('.tinder');
	var allCards = document.querySelectorAll('.tinder--card');
	var nope = document.getElementById('nope');
	var love = document.getElementById('love');
	//implment counter to know which id fits which element
	var counter = 0;

	function initCards(card, index) {
	  var newCards = document.querySelectorAll('.tinder--card:not(.removed)');

	  newCards.forEach(function (card, index) {
		card.style.zIndex = allCards.length - index;
		card.style.transform = 'scale(' + (20 - index) / 20 + ') translateY(-' + 30 * index + 'px)';
		card.style.opacity = (10 - index) / 10;
	  });
	  
	  tinderContainer.classList.add('loaded');
	}

	initCards();

	allCards.forEach(function (el) {
	  var hammertime = new Hammer(el);

	  hammertime.on('pan', function (event) {
		el.classList.add('moving');
	  });

	  hammertime.on('pan', function (event) {
		if (event.deltaX === 0) return;
		if (event.center.x === 0 && event.center.y === 0) return;

		tinderContainer.classList.toggle('tinder_love', event.deltaX > 0);
		tinderContainer.classList.toggle('tinder_nope', event.deltaX < 0);

		var xMulti = event.deltaX * 0.03;
		var yMulti = event.deltaY / 80;
		var rotate = xMulti * yMulti;

		event.target.style.transform = 'translate(' + event.deltaX + 'px, ' + event.deltaY + 'px) rotate(' + rotate + 'deg)';
	  });

	  hammertime.on('panend', function (event) {
		el.classList.remove('moving');
		tinderContainer.classList.remove('tinder_love');
		tinderContainer.classList.remove('tinder_nope');

		var moveOutWidth = document.body.clientWidth;
		var keep = Math.abs(event.deltaX) < 80 || Math.abs(event.velocityX) < 0.5;

		event.target.classList.toggle('removed', !keep);

		if (keep) {
		  event.target.style.transform = '';
		} else {
		  var endX = Math.max(Math.abs(event.velocityX) * moveOutWidth, moveOutWidth);
		  var toX = event.deltaX > 0 ? endX : -endX;
		  var endY = Math.abs(event.velocityY) * moveOutWidth;
		  var toY = event.deltaY > 0 ? endY : -endY;
		  var xMulti = event.deltaX * 0.03;
		  var yMulti = event.deltaY / 80;
		  var rotate = xMulti * yMulti;

		  event.target.style.transform = 'translate(' + toX + 'px, ' + (toY + event.deltaY) + 'px) rotate(' + rotate + 'deg)';
		  initCards();
		}
	  });
	});

	function createButtonListener(love) {
	  return function (event) {
		var cards = document.querySelectorAll('.tinder--card:not(.removed)');
		var moveOutWidth = document.body.clientWidth * 1.5;

		if (!cards.length) return false;

		var card = cards[0];

		card.classList.add('removed');

		if (love) {
		  card.style.transform = 'translate(' + moveOutWidth + 'px, -100px) rotate(-30deg)';
		  //Dev note: Implement proper input from site AKA current user and user swiped on
		  var userName = document.getElementById("currentUser").innerHTML;
		  //Jquery loop each id and use the counter to match the loop to back out
		  $('.currentSwipe').each(function(i,j){
			  if (i == counter){
				   var userLiked = j.innerHTML;
				  checkMatch(userName, userLiked);
			  }
		  });
			
		} else {
			 card.style.transform = 'translate(-' + moveOutWidth + 'px, -100px) rotate(30deg)';
		  //Get the username 
		  var userName = document.getElementById("currentUser").innerHTML;
		  $('.currentSwipe').each(function(i,j){
			  if (i == counter){
				   var userLiked = j.innerHTML;
				   dislikeUpdate(userName, userLiked);
			  }
		  });
		 
		}

		initCards();

		event.preventDefault();
	  };
	}

	/*This function checks for matches and updates the like of the user
	This happens by updating the likes for the current user 
	If it's a match, match is returned
	Input: PCN
	Output: Match or No Match */
	function checkMatch(username, liked){
		  const dbparam = JSON.stringify({"userSwiper":username, "userLiked":liked});
		  const xhttp = new XMLHttpRequest();
		  var myObj;
		  
		  xhttp.onload = function(){		
			myObj = this.responseText;
			counter++;
			if(myObj == "yes"){
				alert("Congratulations! You matched with " + liked + "! An email will arrive shortly");
			}
		  }
		  xhttp.open("POST", "checkmatch.php");
		  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  xhttp.send("x=" + dbparam);
		  
	}
	//User to update dislike if user pressed dislike
	function dislikeUpdate(userName, disliked){
		  const dbparam = JSON.stringify({"userSwiper":userName, "userDisliked":disliked});
		  const xhttp = new XMLHttpRequest();
		  var myObj;
		  
		  xhttp.onload = function(){		
			myObj = this.responseText;
			counter++;
		  }
		  xhttp.open("POST", "updateDislike.php");
		  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		  xhttp.send("x=" + dbparam);
	}

	var nopeListener = createButtonListener(false);
	var loveListener = createButtonListener(true);

	nope.addEventListener('click', nopeListener);
	love.addEventListener('click', loveListener);
 });
 
	