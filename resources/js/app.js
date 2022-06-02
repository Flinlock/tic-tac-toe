// require('./bootstrap');
let gameId;
let gameComplete;
let gameContainer = document.getElementById('game-container');

window.onload = function() {
	newGame();
}

document.getElementById('new-game').onclick = function() {
	window.location.reload();
}

function newGame() {
	gameComplete = false;
	// request a new game from the backend and set the game ID
	fetch(window.location.href + 'games/new').then(response => {
		return response.json();
	}).then(data => {
		gameId = data.id;
		resetGameBoard();
	});
}

function resetGameBoard() {
	emptyElement(gameContainer);

	for(count = 0; count < 9; count++) {
		let position = document.createElement('div');
		position.classList.add('position', 'open');
		position.id = 'position-' + count;
		position.dataset.location = count;
		position.dataset.value = null;
		position.onclick = function(event) {
			playMove(event.target);
		}

		gameContainer.appendChild(position);
	}
}

function playMove(position) {
	if (gameComplete) {
		alert('You must start a new game first!');
		return;
	}

	if (!position.classList.contains('open')) {
		alert('You can only play in a blank space!');
		return;
	}

	updatePosition(position, 'x');

	// Send the move to the backend
	fetch(window.location.href + 'games/' + gameId + '/move?location=' + position.dataset.location + '&value=x', 
	{
		method: 'POST',
		headers: {
			'Accept': 'application/json, text/plain, */*',
			'Content-Type': 'application/json'
		}
	}).then(response => {
		return response.json();
	}).then(data => {
		console.log(data);
		if (data.status == 'complete') {
			finishGame(data);
		} else {
			playComputerMove(data.openPositions);
		}
	});
}

function playComputerMove(openPositions) {
	var location = openPositions[Math.floor(Math.random()*openPositions.length)];
	let position = document.getElementById('position-' + location);
	updatePosition(position, 'o');

	// Send the move to the backend
	fetch(window.location.href + 'games/' + gameId + '/move?location=' + location + '&value=o', 
	{
		method: 'POST',
		headers: {
			'Accept': 'application/json, text/plain, */*',
			'Content-Type': 'application/json'
		}
	}).then(response => {
		return response.json();
	}).then(data => {
		if (data.status == 'complete') {
			finishGame(data);
		} else {
			// allow the human to play
		}
	});
}

function updatePosition(position, value) {
	position.innerHTML = value;
	position.dataset.value = value;
	position.classList.remove('open');
}

function finishGame(data) {
	gameComplete = true;
	if (data.victor == 'x') {
		alert('Congratulations! You beat the computer!');
	} else if (data.victor == 'o') {
		alert('The computer beat you! Try harder next time.');
	} else {
		alert('The cat won this one!');
	}
}

// Get rid of all child elements
function emptyElement(element) {
	while (element.hasChildNodes()) {
		element.removeChild(element.lastChild);
	}
}
