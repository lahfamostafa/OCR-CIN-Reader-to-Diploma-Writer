<video id="webcam" autoplay playsinline width="640" height="480"></video>

<button id="btn-recto">Capturer Recto</button>
<button id="btn-verso">Capturer Verso</button>

<canvas id="canvas" style="display:none;"></canvas>

<script>
const video = document.getElementById('webcam');
const canvas = document.getElementById('canvas');

// 1. Démarrer la caméra (cherche la caméra arrière sur smartphone si dispo)
navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
    .then(stream => { video.srcObject = stream; })
    .catch(err => console.error("Erreur caméra : ", err));

// 2. Fonction pour capturer et envoyer
function capturerDocument(face) {
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Dessine l'image de la vidéo sur le canvas
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convertit en base64 (image/jpeg)
    const base64Image = canvas.toDataURL('image/jpeg');

    // Envoi à Laravel
    fetch('/api/upload-document', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ side: face, image: base64Image })
    })
    .then(res => res.json())
    .then(data => alert(`${face} envoyé avec succès !`));
}

document.getElementById('btn-recto').addEventListener('click', () => capturerDocument('recto'));
document.getElementById('btn-verso').addEventListener('click', () => capturerDocument('verso'));
</script>