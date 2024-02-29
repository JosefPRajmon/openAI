DowlSlide();
function DowlSlide(){
    var messagesDiv = document.querySelector('.messages');
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

// Najděte textovou oblast pomocí její třídy nebo ID
var textarea = document.querySelector('.message');

// Přidejte posluchače událostí 'focus', který se spustí, když uživatel klikne na textovou oblast
textarea.addEventListener('focus', function() {
    // Změňte šířku a výšku textové oblasti
    textarea.style.width = '180px';
    textarea.style.height = '45px';
    document.querySelector('.checkbox').style.display = "none";
    document.querySelector('.send').style.display = "none";
});

textarea.addEventListener('blur', function() {
    textarea.style.width = 'auto';
    textarea.style.height = '20px';
    document.querySelector('.checkbox').style.display = "inline-block";
    document.querySelector('.send').style.display = "inline-block";
});


