<script setup>

defineProps(['event']);

const statusTranslations = {
    published: 'Publié',
    draft: 'Brouillon',
    cancelled: 'Annulé',
};

const getEventStatus = (startDate, endDate) => {
    const now = new Date();
    const start = new Date(startDate);
    const end = new Date(endDate);

    if (now < start) {
        return "À venir";
    } else if (now >= start && now <= end) {
        return "En cours";
    } else {
        return "Passé";
    }
};

</script>

<template>
    <div
        class="max-w-xs bg-white rounded-lg overflow-hidden shadow-lg transition-transform duration-300 hover:transform hover:-translate-y-2 group">
        <div class="relative h-48">
            <img :src="'/storage/' + event.banner_image" alt="Banner" class="w-full h-full object-cover"/>
            <!-- Icône d'édition -->
            <button
                class="absolute top-2 right-2  p-2 rounded-full  text-gray-600 hover:text-blue-600 bg-gray-100 transition duration-200 opacity-0 group-hover:opacity-100 z-10"
                @click="$emit('edit', event)"
            >
                <svg class="h-5 w-5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.232 5.232l3.536 3.536M16.5 3a2.121 2.121 0 112.998 2.998l-11.376 11.376a4 4 0 01-1.414.941l-3.192.797.797-3.192a4 4 0 01.941-1.414L16.5 3z"
                        stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2"/>
                </svg>
            </button>
            <button
                class="absolute top-2 right-12  p-2 rounded-full  text-red-500 bg-gray-100 transition duration-200 opacity-0 group-hover:opacity-100 z-10"
                @click="$emit('delete', event)"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M3 6h18M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2M19 6l1 14a2 2 0 01-2 2H6a2 2 0 01-2-2L5 6m7 1v10"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    />
                </svg>
            </button>
        </div>
        <div class="p-4">
            <h2 class="text-xl font-semibold text-gray-800 truncate">{{ event.title }}</h2>
            <p class="text-sm text-gray-600 mt-2 h-16 overflow-hidden">{{ event.description }}</p>
            <div class="mt-3 text-sm text-gray-500">
                <p><strong>Lieu:</strong> {{ event.location }}</p>
                <p><strong>Date:</strong> {{ new Date(event.start_date).toLocaleString() }} -
                    {{ new Date(event.end_date).toLocaleString() }}</p>
                <div class="flex gap-2">
                    <span :class="{
                      'bg-green-500 text-white': event.status === 'published',
                      'bg-yellow-500 text-white': event.status === 'draft',
                      'bg-red-500 text-white': event.status === 'cancelled'
                    }" class="inline-block px-3 py-1 mt-2 rounded-full text-xs font-semibold capitalize">
                      {{ statusTranslations[event.status] }}
                    </span>
                    <span :class="{
                        'bg-blue-600 text-white': getEventStatus(event.start_date, event.end_date) === 'En cours',
                        'bg-yellow-600 text-white': getEventStatus(event.start_date, event.end_date) === 'À venir',
                        'bg-gray-500 text-white': getEventStatus(event.start_date, event.end_date) === 'Passé'
                      }" class="inline-block px-3 py-1 mt-2 rounded-full text-xs font-semibold capitalize">
                        {{ getEventStatus(event.start_date, event.end_date) }}
                      </span>
                </div>
            </div>

        </div>
    </div>
</template>

<style scoped>
</style>
