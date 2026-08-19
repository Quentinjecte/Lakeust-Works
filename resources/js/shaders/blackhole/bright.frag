precision mediump float;
varying vec2 vUv;
uniform sampler2D tSrc;
uniform float uThresh;
void main(){
  vec3 c = texture2D(tSrc, vUv).rgb;
  float l = dot(c, vec3(0.2126,0.7152,0.0722));
  gl_FragColor = vec4(c * smoothstep(uThresh, uThresh+0.55, l), 1.0);
}
