precision highp float;
varying vec3 vNrm;
varying vec3 vPos;
uniform vec3 uColor;
uniform float uHi, uOccl, uDim, uTime;
void main(){
  vec3 n = normalize(vNrm);
  vec3 v = normalize(-vPos);
  float lam = clamp(dot(n, normalize(vec3(-0.4, 0.25, 0.85))), 0.0, 1.0);
  float rim = pow(1.0 - clamp(dot(n, v), 0.0, 1.0), 2.6);
  vec3 c = uColor * (0.16 + lam*0.42) + uColor * rim * (0.85 + uHi*1.8);
  c += uColor * uHi * 0.30;
  gl_FragColor = vec4(c * uOccl * uDim, 1.0);
}
