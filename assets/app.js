import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

const clickToZoom = document.querySelectorAll(".photo-image");
const zoomedImageContainer = document.querySelector(".zoomed-image");
const zoomedImg = zoomedImageContainer.querySelector("img");

clickToZoom.forEach((img) => {
  img.addEventListener("click", () => {
    zoomedImg.src = img.src;
    zoomedImageContainer.style.display = "flex";
  });
});

zoomedImageContainer.addEventListener("click", () => {
  zoomedImageContainer.style.display = "none";
});
