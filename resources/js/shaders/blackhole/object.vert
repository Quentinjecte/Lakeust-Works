varying vec3 vNrm;
varying vec3 vPos;
void main(){
  vNrm = normalize(normalMatrix * normal);
  vec4 mv = modelViewMatrix * vec4(position, 1.0);
  vPos = mv.xyz;
  gl_Position = projectionMatrix * mv;
}
