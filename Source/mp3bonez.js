window.mp3bonez = (function(){

	return {

		createPlayer: function (
						element, 
						waveElement, 
						timeElement, 
						maskElement,
						loadingElement, 
						width, 
						height, 
						pluginDir, 
						waveColor, 
						progressColor, 
						responsizeLayout) {

			if (!(window.AudioContext || window.webkitAudioContext)) {
	            return;
	        }

			var wavesurfer = Object.create(WaveSurfer);
			var playing = false;

			var playPauseButton = jQuery('#' + element.id + '.mp3-bonez .button:first-child');
			var stopButton = jQuery('#' + element.id + '.mp3-bonez .button:nth-child(2)');

			wavesurfer.init({
			    container: waveElement, // first child of the container
			    waveColor: waveColor || '#CDCDCD',
			    progressColor: progressColor || '#474747',
			    dragSelection : false,
		    	loopSelection : false,
		    	height: height,
		    	cursorWidth: 0
			});

			function stopPlayer(){
				playing = false;
				wavesurfer.stop();

				playPauseButton.find('img').attr('src', pluginDir + 'content/play-white.png');	
			}
			function pausePlayer(){
				playing = false;
				wavesurfer.pause();

				playPauseButton.find('img').attr('src', pluginDir + 'content/play-white.png');				
			}
			function startPlayer(){
		    	playing = true;
		    	wavesurfer.play();

		    	playPauseButton.find('img').attr('src', pluginDir + 'content/pause-white.png');
			}
			function resizePlayer(waveLoaded){
				var width = jQuery(element.parentElement).width();
				jQuery(element).width(width);
				jQuery(element).find('.mask').width(width + 2);
				jQuery(element).find('.button-container').width(width + 2);
				jQuery(loadingElement).css('left', ((width / 2) - 50).toString() + 'px');
				wavesurfer.drawer.containerWidth = wavesurfer.drawer.container.clientWidth;
				if (waveLoaded)
					wavesurfer.drawBuffer();
			}

			playPauseButton.on('click', function () {
				if (!playing)
					startPlayer(this);

				else 
					pausePlayer(this);
			});
			stopButton.on('click', function () {
				stopPlayer();
			});		


			// play progress
			wavesurfer.on('progress', function(progress){
				var time = wavesurfer.getCurrentTime();
				var duration = wavesurfer.getDuration();

				timeElement.innerHTML = '<p>' + time.toFixed(2) + ':' + duration.toFixed(2) + '</p>';
			});

			// wave source ready
			wavesurfer.on('ready', function(result){
				timeElement.innerHTML = '<p>0.00:' + wavesurfer.getDuration().toFixed(2) + '</p>';

				jQuery(timeElement).css('opacity', 0.5);
				jQuery(loadingElement).css('display', 'none');
				jQuery(element).css('opacity', 1);

				if (responsizeLayout)
					resizePlayer(true);				
			}); 

			// finished
			wavesurfer.on('finish', function(){
				stopPlayer();
			});

			// responsive layout
			if (responsizeLayout){
				jQuery(window).on('resize', function(){
					resizePlayer(true);
				});
			}

			jQuery(timeElement).css('opacity', 0);
			wavesurfer.load(element.getAttribute('src'));
			if (responsizeLayout)
				resizePlayer(false);
		}
	}
})();