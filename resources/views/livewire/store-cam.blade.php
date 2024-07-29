<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <style>
        #video {
            width: 800px;
        }
        #canvas {
            width: 800px
        }
    </style>
    <h2>Test</h2>
    <div>
        <h1>Cashier Application</h1>
        <video id="video" width="640" height="480" autoplay muted></video>
        <canvas id="canvas" width="640" height="480"></canvas>
        <button">Start Detection</button>
    </div>

    @assets
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/coco-ssd"></script>
    @endassets
    @script
    <script>
    let video;
    let model;

    async function setupCamera() {
        video = document.getElementById('video');
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        return new Promise((resolve) => {
            video.onloadedmetadata = () => { resolve(video); };
        });
    }

    async function loadModel() {
        model = await cocoSsd.load();
    }

    async function startDetection() {
        await setupCamera();
        await loadModel();
        video.play();
        detectFrame();
    }

    async function detectFrame() {
        const predictions = await model.detect(video);
        renderPredictions(predictions);
        requestAnimationFrame(detectFrame);
    }

    function renderPredictions(predictions) {
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        predictions.forEach(prediction => {
            const [x, y, width, height] = prediction.bbox;
            ctx.beginPath();
            ctx.rect(x, y, width, height);
            ctx.lineWidth = 1;
            ctx.strokeStyle = 'green';
            ctx.fillStyle = 'green';
            ctx.stroke();
            ctx.fillText(
                `${prediction.class} - ${Math.round(prediction.score * 100)}%`,
                x,
                y > 10 ? y - 5 : 10
            );
        });
    }
    startDetection();
    </script>
    @endscript
</div>
