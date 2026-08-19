precision mediump float;
varying vec2 vUvG;
uniform vec3 uColor;
uniform float uHi, uOccl, uDim;
void main(){
  float d = length(vUvG - 0.5)*2.0;
  float a = pow(clamp(1.0-d, 0.0, 1.0), 3.0);
  gl_FragColor = vec4(uColor * a * (0.28 + uHi*1.5) * uOccl * uDim, 1.0);
}
