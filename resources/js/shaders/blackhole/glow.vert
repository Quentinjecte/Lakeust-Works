varying vec2 vUvG;
void main(){ vUvG = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0); }
