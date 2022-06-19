import * as THREE from 'three';
import { GLTFLoader } from "./three.js-master/examples/jsm/loaders/GLTFLoader";

const loader = new GLTFLoader();

loader.load('./habitacion.glb', function (glb) {
    scene.add(glb);
});
