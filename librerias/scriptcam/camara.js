const width = 320;
const height = 400;

const constraints = {
	video: { width, height },
};

const divScreenshot = document.querySelector(".screenshot");
const captureVideoButton = document.querySelector(".capture-button");
const screenshotButton = document.querySelector(".screenshot-button");
const img = document.querySelector("#screenshot-img");
const video = document.querySelector("video");
const canvas = document.createElement("canvas");

divScreenshot.style.display = "grid";
divScreenshot.style.width = `${width * 2.1}px`;
divScreenshot.style.gridTemplateColumns = "repeat(2, 1fr)";

captureVideoButton.textContent = "Encender Camara";
screenshotButton.textContent = "Tomar Foto";

captureVideoButton.style.border = "2px solid red";
captureVideoButton.style.color = "white";
captureVideoButton.style.backgroundColor = "red";
captureVideoButton.style.padding = "1rem 0";
captureVideoButton.style.width = `${width}px`;
captureVideoButton.style.fontSize = `1.2rem`;

screenshotButton.style.border = "2px solid rgb(2, 56, 129)";
screenshotButton.style.color = "white";
screenshotButton.style.backgroundColor = "rgb(2, 56, 129)";
screenshotButton.style.padding = "1rem 0";
screenshotButton.style.width = `${width}px`;
screenshotButton.style.fontSize = `1.2rem`;

video.style.width = `${width}px`;
video.style.height = `${height}px`;

const handleError = (error) => {
	console.log("navigator.getUserMedia error: ", error);
};
const handleSuccess = (stream) => {
	screenshotButton.disabled = false;
	video.srcObject = stream;
};

captureVideoButton.onclick = () => {
	navigator.mediaDevices
		.getUserMedia(constraints)
		.then(handleSuccess)
		.catch(handleError);
};

screenshotButton.onclick = video.onclick = function () {
	canvas.width = video.videoWidth;
	canvas.height = video.videoHeight;
	canvas.getContext("2d").drawImage(video, 0, 0);
	img.src = canvas.toDataURL("image/jpeg");
	console.log(canvas.toDataURL("image/jpeg"));
};
