/* Raymarched Schwarzschild geodesics: lensed background, accretion disk, photon ring.
   STEPS is prepended by welcome-blackhole.js (quality tier), NOISE chunk too. */
precision highp float;
varying vec2 vUv;
uniform vec2  uRes;
uniform float uTime;
uniform vec3  uCamPos;
uniform mat3  uCamBasis;
uniform float uTanHalfFov;
uniform float uDiskBright;
uniform float uDiskMul;
uniform float uDiskThick;
uniform float uLens;
uniform float uBgFade;
uniform float uCapture;
uniform float uSpin;
uniform float uEscape;
uniform vec3  uHot;
uniform vec3  uMid;
uniform vec3  uCool;

const float R_IN  = 2.7;
const float R_OUT = 13.0;

vec3 starField(vec3 d){
  vec3 col = vec3(0.0);
  for(int k=0;k<3;k++){
    float sc = 60.0 + float(k)*130.0;
    vec3 p = d*sc;
    vec3 i = floor(p), f = fract(p);
    for(int x=-1;x<=1;x++) for(int y=-1;y<=1;y++) for(int z=-1;z<=1;z++){
      vec3 g = vec3(float(x),float(y),float(z));
      vec3 o = vec3(hash31(i+g), hash31(i+g+11.7), hash31(i+g+31.1));
      float br = hash31(i+g+77.3);
      if(br < 0.955) continue;
      float dd = length(f-g-o);
      float s = smoothstep(0.10, 0.0, dd);
      float tw = 0.72 + 0.28*sin(uTime*(0.6+br*2.4) + br*44.0);
      vec3 tint = mix(vec3(0.72,0.78,1.0), vec3(1.0,0.93,0.80), hash31(i+g+5.5));
      col += tint * s*s * tw * (0.55 + float(2-k)*0.42);
    }
  }
  return col*1.15;
}

vec3 nebula(vec3 d){
  float n  = fbm3(d*1.6 + 4.0);
  float n2 = fbm3(d*3.4 - 9.0);
  float m  = smoothstep(0.45, 0.95, n) * 0.85 + smoothstep(0.55, 1.0, n2)*0.4;
  vec3 a = vec3(0.108, 0.098, 0.196);
  vec3 b = vec3(0.056, 0.070, 0.104);
  return mix(b, a, n2) * m * 0.12;
}

vec3 diskSample(vec3 hp, vec3 rayDir, out float dens){
  float r   = length(hp.xz);
  float u   = clamp((r-R_IN)/(R_OUT-R_IN), 0.0, 1.0);
  float phi = atan(hp.z, hp.x);
  float om  = uSpin * 2.6 * pow(max(r,0.6), -1.5);
  float pa  = phi + om*uTime;

  float band = fbm(vec2(pa*1.9, u*3.1) + vec2(0.0, uTime*0.02));
  float fine = fbm(vec2(pa*9.0, u*17.0) - vec2(uTime*0.10, 0.0));
  float wisp = fbm(vec2(pa*24.0, u*40.0) - vec2(uTime*0.26, 0.0));
  float d = band*0.62 + fine*0.30 + wisp*0.16;
  d = pow(clamp(d*1.5-0.42, 0.0, 1.6), 1.5);

  float inner = smoothstep(0.0, 0.16, u) * (0.35 + 0.65*smoothstep(0.0,0.05,u));
  float outer = 1.0 - smoothstep(0.34, 1.0, u);
  d *= inner*outer;
  d *= 1.0 + 1.9*exp(-u*9.0);
  dens = d;

  vec3 col = mix(uHot, uMid, smoothstep(0.02, 0.30, u));
  col = mix(col, uCool, smoothstep(0.46, 0.98, u));
  col *= 1.0 + 4.2*exp(-u*5.5);

  vec3 vel = normalize(cross(vec3(0.0,1.0,0.0), hp)) * (0.62*pow(max(r,1.0),-0.5)) * uSpin;
  float dop = 1.0 / max(1.0 - dot(vel, -rayDir), 0.22);
  float beam = pow(clamp(dop, 0.30, 3.4), 2.3);
  col *= mix(vec3(0.86,0.90,1.06), vec3(1.10,0.90,0.72), clamp(1.0/dop, 0.0, 1.0));

  return col * beam;
}

void main(){
  vec2 uv = (gl_FragCoord.xy - 0.5*uRes) / uRes.y;
  vec3 rd = normalize(uCamBasis * vec3(uv * uTanHalfFov * 2.0, -1.0));
  vec3 p  = uCamPos;

  vec3 L  = cross(p, rd);
  float b0 = length(L);
  float h2 = dot(L, L) * uLens;

  float jit = ign(gl_FragCoord.xy + fract(uTime)*vec2(37.0, 17.0));
  p += rd * (jit - 0.5) * 0.9;

  vec3 acc = vec3(0.0);
  float trans = 1.0;
  float minR = 1e9;
  bool captured = false;
  bool escaped  = false;

  for(int i=0;i<STEPS;i++){
    float r2 = dot(p,p);
    float r  = sqrt(r2);
    minR = min(minR, r);
    if(r < 1.0){ captured = true; break; }
    if(r > uEscape && dot(p, rd) > 0.0){ escaped = true; break; }

    float dt = clamp(0.11*r, 0.02, 6.0) * clamp((r-0.98)/1.8, 0.45, 1.0) * mix(0.55, 1.0, smoothstep(2.0, 4.2, r)) * (0.78 + 0.44*jit);
    vec3 a  = -1.5 * h2 * p / (r2*r2*r);
    vec3 np = p + rd*dt + 0.5*a*dt*dt;

    if(p.y*np.y < 0.0 && trans > 0.004){
      float f  = -p.y / (np.y - p.y);
      vec3 hp  = mix(p, np, clamp(f,0.0,1.0));
      float rr = length(hp.xz);
      if(rr > R_IN && rr < R_OUT){
        float dens;
        vec3 e = diskSample(hp, normalize(np-p), dens);
        float ang = abs(np.y - p.y) / max(length(np - p), 1e-4);
        dens *= smoothstep(0.0, 0.14, ang);
        float grav = clamp((rr-1.3)/4.2, 0.10, 1.0) * (0.40 + 0.60*(1.0 - smoothstep(4.6, 13.0, rr)));
        acc += e * dens * trans * uDiskBright * grav;
        trans *= exp(-dens * uDiskThick);
      }
    }
    rd = normalize(rd + a*dt);
    p = np;
  }

  if(!escaped && !captured && length(p) < 2.4) captured = true;

  vec3 col = acc;

  if(!captured){
    col += (starField(rd) + nebula(rd)) * trans * uBgFade;

    float bc = 2.598 * uLens;
    float rw = 0.34 * uLens + 0.10;
    float ringF = exp(-pow((b0 - bc)/rw, 2.0)) * step(bc*0.90, b0);
    col += mix(uHot, uMid, 0.20) * ringF * 6.6 * uDiskBright;

    float t1 = exp(-max(b0 - bc, 0.0) * (0.95/uLens));
    float t2 = exp(-max(b0 - bc, 0.0) * (0.15/uLens));
    col += mix(uHot, uMid, 0.45) * t1 * 1.70 * uDiskBright;
    col += mix(uMid, uCool, 0.35) * t2 * 0.44 * uDiskBright;
  }

  col += uCapture * mix(uHot, uCool, 0.4) * 0.05 * (1.0-smoothstep(1.0,4.0,minR));

  gl_FragColor = vec4(max(col, 0.0) * uDiskMul, 1.0);
}
