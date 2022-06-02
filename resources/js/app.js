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

/**
 * Request a new game and set the ID
 */
function newGame() {
	gameComplete = false;
	fetch(window.location.href + 'games/new').then(response => {
		return response.json();
	}).then(data => {
		gameId = data.id;
		createGameBoard();
	});
}

/**
 * Create the nine position tiles
 */
function createGameBoard() {
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

/**
 * Execute a player move and then handle the updated game status
 * @param {obj} position DOM element that was clicked
 */
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
		if (data.status == 'complete') {
			finishGame(data);
		} else {
			playComputerMove(data.openPositions);
		}
	});
}

/**
 * Execute a computer move in a random open position
 * @param {array} openPositions Available positions for the computer to choose from
 */
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

/**
 * Update a game board position to indicate it has been played in
 * @param {obj} position DOM element to be updated
 * @param {string} value X or O 
 */
function updatePosition(position, value) {
	position.innerHTML = value;
	position.dataset.value = value;
	position.classList.remove('open');
}

/**
 * Process the game results and announce the winner
 * @param {obj} data the game results
 */
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
