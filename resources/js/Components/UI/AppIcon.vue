<script setup>
defineProps({
    name: { type: String, required: true },
    size: { type: [String, Number], default: 20 },
});
</script>

<template>
    <svg
        :width="size"
        :height="size"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
    >
        <template v-if="name === 'dashboard'">
            <rect x="3" y="3" width="7" height="7" rx="1.5" />
            <rect x="14" y="3" width="7" height="7" rx="1.5" />
            <rect x="3" y="14" width="7" height="7" rx="1.5" />
            <rect x="14" y="14" width="7" height="7" rx="1.5" />
        </template>
        <template v-else-if="name === 'clients'">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
        </template>
        <template v-else-if="name === 'websites' || name === 'globe'">
            <circle cx="12" cy="12" r="9" />
            <path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18" />
        </template>
        <template v-else-if="name === 'tickets'">
            <path
                d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4a3 3 0 0 0 0 6v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4a3 3 0 0 0 0-6V5Z"
            />
            <path d="M13 5v2M13 11v2M13 17v2" />
        </template>
        <template v-else-if="name === 'maintenance'">
            <path
                d="M14.7 6.3a4 4 0 0 0-5-5l2.1 2.1-8.4 8.4a2 2 0 1 0 2.8 2.8l8.4-8.4 2.1 2.1a4 4 0 0 0-2-2Z"
            />
            <path d="m15 13 6 6-2 2-6-6" />
        </template>
        <template v-else-if="name === 'reports'">
            <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" />
            <path d="M14 2v6h6M8 13h8M8 17h8M8 9h2" />
        </template>
        <template v-else-if="name === 'inbox'">
            <path
                d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5v-11Z"
            />
            <path d="M4 14h4l1.5 2h5l1.5-2h4" />
        </template>
        <template v-else-if="name === 'menu'">
            <path d="M4 7h16M4 12h16M4 17h16" />
        </template>
        <template v-else-if="name === 'close'">
            <path d="m6 6 12 12M18 6 6 18" />
        </template>
        <template v-else-if="name === 'logout'">
            <path d="M10 17l5-5-5-5M15 12H3" />
            <path d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5" />
        </template>
        <template v-else-if="name === 'building'">
            <path d="M3 21h18M5 21V5l7-3 7 3v16M9 9h1M14 9h1M9 13h1M14 13h1M9 17h1M14 17h1" />
        </template>
        <template v-else-if="name === 'alert'">
            <path
                d="M10.3 3.9 2.5 18a2 2 0 0 0 1.8 3h15.4a2 2 0 0 0 1.8-3L13.7 3.9a2 2 0 0 0-3.4 0Z"
            />
            <path d="M12 9v4M12 17h.01" />
        </template>
        <template v-else-if="name === 'clock'">
            <circle cx="12" cy="12" r="9" />
            <path d="M12 7v5l3 2" />
        </template>
        <template v-else-if="name === 'server'">
            <rect x="3" y="4" width="18" height="6" rx="2" />
            <rect x="3" y="14" width="18" height="6" rx="2" />
            <path d="M7 7h.01M7 17h.01" />
        </template>
        <template v-else-if="name === 'check'">
            <path d="m5 12 4 4L19 6" />
        </template>
        <template v-else-if="name === 'plus'">
            <path d="M12 5v14M5 12h14" />
        </template>
        <template v-else-if="name === 'arrow-up'">
            <path d="m18 15-6-6-6 6M12 9v10" />
        </template>
    </svg>
</template>
