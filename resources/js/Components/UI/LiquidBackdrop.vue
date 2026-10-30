<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

// Adapted from the original ChargeOS optical study. Keeps the WebGL effect
// separate from Laravel/Inertia content, with a static CSS fallback.
const canvas = ref(null);
const available = ref(false);
const vertexSource = "\nattribute vec2 position;\nvoid main() {\n  gl_Position = vec4(position, 0.0, 1.0);\n}";
const fragmentSource = "\nprecision highp float;\n\nuniform vec2 u_resolution;\nuniform vec2 u_pointer;\nuniform float u_time;\nuniform float u_theme;\nuniform vec4 u_glass[5];\nuniform float u_active[5];\n\n#define PI 3.14159265359\n\nfloat sdRoundBox(vec2 p, vec2 b, float r) {\n  vec2 q = abs(p) - b + r;\n  return min(max(q.x, q.y), 0.0) + length(max(q, 0.0)) - r;\n}\n\nfloat softCircle(vec2 p, vec2 c, float r, float feather) {\n  return 1.0 - smoothstep(r - feather, r + feather, length(p - c));\n}\n\nvec3 scene(vec2 uv) {\n  float aspect = u_resolution.x / u_resolution.y;\n  vec2 p = vec2((uv.x - 0.5) * aspect, uv.y - 0.5);\n  vec3 lightBase = vec3(0.925, 0.938, 0.925);\n  vec3 darkBase = vec3(0.055, 0.065, 0.068);\n  vec3 colorBase = vec3(0.89, 0.91, 0.87);\n  vec3 col = mix(lightBase, darkBase, step(0.5, u_theme) * (1.0 - step(1.5, u_theme)));\n  col = mix(col, colorBase, step(1.5, u_theme));\n\n  float horizon = smoothstep(-0.14, -0.12, p.y);\n  vec3 sky = mix(vec3(0.85, 0.89, 0.88), vec3(0.985, 0.97, 0.91), uv.y);\n  vec3 floorCol = vec3(0.79, 0.80, 0.75);\n  if (u_theme > 0.5 && u_theme < 1.5) {\n    sky = mix(vec3(0.025, 0.034, 0.04), vec3(0.12, 0.145, 0.15), uv.y);\n    floorCol = vec3(0.035, 0.042, 0.045);\n  }\n  if (u_theme > 1.5) {\n    sky = mix(vec3(0.70, 0.84, 0.84), vec3(1.0, 0.91, 0.74), uv.y);\n    floorCol = vec3(0.77, 0.76, 0.67);\n  }\n  col = mix(floorCol, sky, horizon);\n\n  float windowA = smoothstep(0.015, 0.0, abs(p.x + 0.58)) * smoothstep(-0.11, -0.08, p.y);\n  float windowB = smoothstep(0.012, 0.0, abs(p.x - 0.49)) * smoothstep(-0.11, -0.08, p.y);\n  float beam = smoothstep(0.48, 0.0, abs(p.x + p.y * 0.56 - 0.13)) * smoothstep(-0.12, 0.30, p.y);\n  col += vec3(1.0, 0.90, 0.67) * beam * (u_theme > 0.5 && u_theme < 1.5 ? 0.08 : 0.20);\n  col += vec3(1.0) * (windowA + windowB) * (u_theme > 0.5 && u_theme < 1.5 ? 0.08 : 0.32);\n\n  vec2 floorP = vec2(p.x / max(0.16, -p.y + 0.13), 0.22 / max(0.16, -p.y + 0.13));\n  float gx = 1.0 - smoothstep(0.014, 0.038, abs(fract(floorP.x * 0.42) - 0.5));\n  float gy = 1.0 - smoothstep(0.012, 0.04, abs(fract(floorP.y * 0.55) - 0.5));\n  float grid = max(gx, gy) * (1.0 - horizon) * 0.12;\n  col -= grid;\n\n  float column = 1.0 - smoothstep(0.115, 0.13, abs(p.x + 0.78));\n  column *= smoothstep(-0.13, -0.08, p.y);\n  col = mix(col, u_theme > 0.5 && u_theme < 1.5 ? vec3(0.10, 0.12, 0.12) : vec3(0.72, 0.73, 0.68), column * 0.72);\n\n  if (u_theme > 1.5) {\n    col = mix(col, vec3(0.12, 0.58, 0.68), softCircle(p, vec2(-0.58, 0.22), 0.25, 0.22) * 0.66);\n    col = mix(col, vec3(0.94, 0.39, 0.21), softCircle(p, vec2(0.68, -0.16), 0.31, 0.26) * 0.58);\n    col = mix(col, vec3(0.86, 0.73, 0.20), softCircle(p, vec2(0.20, 0.38), 0.22, 0.21) * 0.40);\n  }\n\n  float grain = fract(sin(dot(gl_FragCoord.xy, vec2(12.9898, 78.233))) * 43758.5453);\n  col += (grain - 0.5) * 0.012;\n  return col;\n}\n\nvec3 glassSample(vec2 uv, vec2 local, vec2 halfSize, float edge, float layer) {\n  float radial = dot(local / max(halfSize, vec2(0.001)), local / max(halfSize, vec2(0.001)));\n  vec2 normal = normalize(local / max(halfSize * halfSize, vec2(0.001)) + vec2(0.0001));\n  float lens = (1.0 - smoothstep(0.04, 1.7, radial)) * (0.006 + layer * 0.0025);\n  vec2 offset = normal * lens;\n  float dispersion = 0.0014 + edge * 0.0018;\n  vec3 refracted;\n  refracted.r = scene(uv + offset + normal * dispersion).r;\n  refracted.g = scene(uv + offset).g;\n  refracted.b = scene(uv + offset - normal * dispersion).b;\n\n  vec2 pointerUv = u_pointer / u_resolution;\n  vec2 lightDir = normalize(pointerUv - uv + vec2(0.0001));\n  float spec = pow(max(0.0, dot(normalize(vec2(-normal.x, normal.y)), lightDir)), 22.0);\n  spec *= smoothstep(0.52, 0.03, distance(pointerUv, uv));\n  float rim = smoothstep(0.14, 0.0, edge);\n  float lowerRim = rim * smoothstep(-0.2, 0.8, -normal.y);\n  vec3 tint = u_theme > 0.5 && u_theme < 1.5 ? vec3(0.72, 0.90, 0.92) : vec3(0.96, 1.0, 0.98);\n  refracted += tint * (rim * 0.13 + lowerRim * 0.09 + spec * 0.52);\n  refracted -= vec3(0.03, 0.045, 0.05) * smoothstep(0.02, 0.11, edge) * smoothstep(0.19, 0.08, edge);\n  return refracted;\n}\n\nvoid main() {\n  vec2 uv = gl_FragCoord.xy / u_resolution;\n  vec3 col = scene(uv);\n\n  for (int i = 0; i < 5; i++) {\n    if (u_active[i] > 0.5) {\n      vec2 center = u_glass[i].xy;\n      vec2 size = u_glass[i].zw;\n      vec2 local = uv - center;\n      local.x *= u_resolution.x / u_resolution.y;\n      vec2 scaledSize = vec2(size.x * u_resolution.x / u_resolution.y, size.y);\n      float radius = min(scaledSize.y * 0.48, scaledSize.x * 0.48);\n      float d = sdRoundBox(local, scaledSize * 0.5, radius);\n      float aa = 1.5 / u_resolution.y;\n      if (d < aa) {\n        float mask = 1.0 - smoothstep(-aa, aa, d);\n        float edge = abs(d);\n        vec3 glass = glassSample(uv, local, scaledSize * 0.5, edge, float(i));\n        col = mix(col, glass, mask * 0.93);\n      }\n    }\n  }\n\n  col *= 1.0 - 0.10 * smoothstep(0.38, 0.92, distance(uv, vec2(0.5)));\n  col = pow(max(col, 0.0), vec3(0.94));\n  gl_FragColor = vec4(col, 1.0);\n}";
let cleanup = () => {};

onMounted(() => {
    const surface = canvas.value;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const reducedTransparency = window.matchMedia('(prefers-reduced-transparency: reduce)');
    if (!surface || reducedMotion.matches || reducedTransparency.matches) return;

    const gl = surface.getContext('webgl', {
        alpha: false,
        antialias: false,
        powerPreference: 'low-power',
        preserveDrawingBuffer: false,
    });
    if (!gl) return;

    let program;
    let vertex;
    let fragment;
    let buffer;
    let frame = 0;
    let destroyed = false;
    const pointer = { x: window.innerWidth * .70, y: window.innerHeight * .28 };
    const smooth = { ...pointer };

    const compile = (type, source) => {
        const shader = gl.createShader(type);
        if (!shader) throw new Error('No se pudo crear el shader');
        gl.shaderSource(shader, source);
        gl.compileShader(shader);
        if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
            const error = gl.getShaderInfoLog(shader);
            gl.deleteShader(shader);
            throw new Error(error || 'Shader inválido');
        }
        return shader;
    };

    try {
        vertex = compile(gl.VERTEX_SHADER, vertexSource);
        fragment = compile(gl.FRAGMENT_SHADER, fragmentSource);
        program = gl.createProgram();
        if (!program) throw new Error('No se pudo crear el programa WebGL');
        gl.attachShader(program, vertex);
        gl.attachShader(program, fragment);
        gl.linkProgram(program);
        if (!gl.getProgramParameter(program, gl.LINK_STATUS)) throw new Error('Link fallido');
        buffer = gl.createBuffer();
        if (!buffer) throw new Error('Buffer no disponible');
        gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1, -1, 3, -1, -1, 3]), gl.STATIC_DRAW);
    } catch (error) {
        console.warn('WebOps: utilizando fondo CSS accesible', error);
        if (buffer) gl.deleteBuffer(buffer);
        if (program) gl.deleteProgram(program);
        if (vertex) gl.deleteShader(vertex);
        if (fragment) gl.deleteShader(fragment);
        return;
    }

    const uniforms = {
        resolution: gl.getUniformLocation(program, 'u_resolution'),
        pointer: gl.getUniformLocation(program, 'u_pointer'),
        time: gl.getUniformLocation(program, 'u_time'),
        theme: gl.getUniformLocation(program, 'u_theme'),
        glass: gl.getUniformLocation(program, 'u_glass'),
        active: gl.getUniformLocation(program, 'u_active'),
    };
    const position = gl.getAttribLocation(program, 'position');
    const started = performance.now();
    let lastDraw = 0;
    let stopWhenSettled = 0;

    const draw = (timestamp) => {
        frame = 0;
        if (destroyed || document.hidden || reducedMotion.matches || reducedTransparency.matches) return;
        // Cap pixels, not the interaction frequency. Full screen blur can be expensive.
        const dpr = Math.min(window.devicePixelRatio || 1, 1.35);
        const w = Math.max(1, Math.round(window.innerWidth * dpr));
        const h = Math.max(1, Math.round(window.innerHeight * dpr));
        if (surface.width !== w || surface.height !== h) {
            surface.width = w;
            surface.height = h;
        }
        smooth.x += (pointer.x - smooth.x) * .16;
        smooth.y += (pointer.y - smooth.y) * .16;
        gl.viewport(0, 0, w, h);
        gl.useProgram(program);
        gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
        gl.enableVertexAttribArray(position);
        gl.vertexAttribPointer(position, 2, gl.FLOAT, false, 0, 0);
        gl.uniform2f(uniforms.resolution, w, h);
        gl.uniform2f(uniforms.pointer, smooth.x * dpr, smooth.y * dpr);
        gl.uniform1f(uniforms.time, (timestamp - started) / 1000);
        gl.uniform1f(uniforms.theme, 0);
        const values = new Float32Array(20);
        const active = new Float32Array(5);
        const eligible = Array.from(document.querySelectorAll('[data-glass-optic]'))
            .filter((node) => {
                const rect = node.getBoundingClientRect();
                return rect.width > 0 && rect.height > 0 && rect.bottom > 0 && rect.top < window.innerHeight && rect.right > 0 && rect.left < window.innerWidth;
            }).slice(0, 5);
        eligible.forEach((node, index) => {
            const box = node.getBoundingClientRect();
            values[index * 4] = (box.left + box.width / 2) / window.innerWidth;
            values[index * 4 + 1] = 1 - (box.top + box.height / 2) / window.innerHeight;
            values[index * 4 + 2] = box.width / window.innerWidth;
            values[index * 4 + 3] = box.height / window.innerHeight;
            active[index] = 1;
        });
        gl.uniform4fv(uniforms.glass, values);
        gl.uniform1fv(uniforms.active, active);
        gl.drawArrays(gl.TRIANGLES, 0, 3);
        available.value = true;
        lastDraw = timestamp;
        const stillMoving = Math.abs(pointer.x - smooth.x) + Math.abs(pointer.y - smooth.y) > .4;
        if (stillMoving && stopWhenSettled++ < 90) schedule();
        else stopWhenSettled = 0;
    };

    const schedule = () => {
        if (!frame && !destroyed && !document.hidden) frame = requestAnimationFrame(draw);
    };
    const move = (event) => {
        pointer.x = event.clientX;
        pointer.y = window.innerHeight - event.clientY;
        stopWhenSettled = 0;
        schedule();
    };
    const change = () => { stopWhenSettled = 0; schedule(); };
    const contextLost = (event) => { event.preventDefault(); available.value = false; cancelAnimationFrame(frame); frame = 0; };
    const contextRestored = () => { available.value = false; }; // CSS fallback until navigation remount
    window.addEventListener('pointermove', move, { passive: true });
    window.addEventListener('resize', change, { passive: true });
    window.addEventListener('scroll', change, { passive: true, capture: true });
    document.addEventListener('visibilitychange', change);
    reducedMotion.addEventListener('change', change);
    reducedTransparency.addEventListener('change', change);
    surface.addEventListener('webglcontextlost', contextLost);
    surface.addEventListener('webglcontextrestored', contextRestored);
    schedule();
    cleanup = () => {
        destroyed = true;
        cancelAnimationFrame(frame);
        window.removeEventListener('pointermove', move);
        window.removeEventListener('resize', change);
        window.removeEventListener('scroll', change, true);
        document.removeEventListener('visibilitychange', change);
        reducedMotion.removeEventListener('change', change);
        reducedTransparency.removeEventListener('change', change);
        surface.removeEventListener('webglcontextlost', contextLost);
        surface.removeEventListener('webglcontextrestored', contextRestored);
        gl.deleteBuffer(buffer);
        gl.deleteProgram(program);
        gl.deleteShader(vertex);
        gl.deleteShader(fragment);
    };
});
onUnmounted(() => cleanup());
</script>

<template>
    <canvas
        ref="canvas"
        class="webops-optical-canvas"
        :class="{ 'is-visible': available }"
        aria-hidden="true"
    />
</template>
