precision highp float;
varying vec2 vUv;
uniform sampler2D tScene, tBloomA, tBloomB;
uniform float uBloom, uVignette, uAberr, uGrain, uTime, uFade, uExposure;
uniform vec2 uRes;
float ign2(vec2 p){ return fract(52.9829189 * fract(0.06711056*p.x + 0.00583715*p.y)); }
vec3 aces(vec3 x){
  const float a=2.51,b=0.03,c=2.43,d=0.59,e=0.14;
  return clamp((x*(a*x+b))/(x*(c*x+d)+e), 0.0, 1.0);
}
void main(){
  vec2 uv = vUv;
  vec2 d = uv - 0.5;
  float r2 = dot(d,d);
  float ab = uAberr * (0.0025 + r2*0.020);
  vec3 col;
  col.r = texture2D(tScene, uv + d*ab).r;
  col.g = texture2D(tScene, uv).g;
  col.b = texture2D(tScene, uv - d*ab).b;
  col += (texture2D(tBloomA, uv).rgb*0.70 + texture2D(tBloomB, uv).rgb*0.42) * uBloom;
  col *= uExposure;
  col = aces(col);
  col *= 1.0 - uVignette*smoothstep(0.16, 0.86, r2);
  float g = ign2(gl_FragCoord.xy + fract(uTime)*vec2(113.0, 71.0));
  col += (g-0.5)*uGrain;
  col *= uFade;
  col = pow(max(col,0.0), vec3(0.4545));
  col += (g - 0.5) / 255.0;
  gl_FragColor = vec4(col, 1.0);
}
