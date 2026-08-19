uniform float uTime;
uniform vec3 uColorA;
uniform vec3 uColorB;

varying vec2 vUv;

void main() {
    float dist = distance(vUv, vec2(0.5));
    float pulse = 0.5 + 0.5 * sin(uTime * 2.0 - dist * 12.0);
    vec3 color = mix(uColorA, uColorB, pulse);

    float glow = smoothstep(0.5, 0.0, dist);
    gl_FragColor = vec4(color * glow, glow);
}
