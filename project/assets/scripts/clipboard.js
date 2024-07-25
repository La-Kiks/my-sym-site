import ClipboardJS from "clipboard";

// Discord copy  button
const discordButton = document.getElementById('discord_button');
const discordDiv = document.getElementById('copy_discord');

if (discordButton && discordDiv){
    discordButton.addEventListener('click', function (){
        discordDiv.hidden = false;
        setTimeout(function() {
            discordDiv.hidden = true;
        }, 1000);
    });
}


// Google copy  button
const googleButton = document.getElementById('google_button');
const googleDiv = document.getElementById('copy_google');

if (googleButton && googleDiv){
    googleButton.addEventListener('click', function (){
        googleDiv.hidden = false;
        setTimeout(function() {
            googleDiv.hidden = true;
        }, 1000);
    });
}


// Clipboard
let clipboard = new ClipboardJS('.btn');

clipboard.on('success', function(e) {
    // console.info('Action:', e.action);
    // console.info('Text:', e.text);
    // console.info('Trigger:', e.trigger);

    e.clearSelection();
});

clipboard.on('error', function(e) {
    console.error('Action:', e.action);
});