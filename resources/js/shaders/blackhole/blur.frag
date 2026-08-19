precision mediump float;
varying vec2 vUv;
uniform sampler2D tSrc;
uniform vec2 uDir;
void main(){
  vec3 s = texture2D(tSrc, vUv).rgb * 0.2270270270;
  s += (texture2D(tSrc, vUv + uDir*1.3846153846).rgb + texture2D(tSrc, vUv - uDir*1.3846153846).rgb) * 0.3162162162;
  s += (texture2D(tSrc, vUv + uDir*3.2307692308).rgb + texture2D(tSrc, vUv - uDir*3.2307692308).rgb) * 0.0702702703;
  gl_FragColor = vec4(s, 1.0);
}
