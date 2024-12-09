<script lang="ts" setup>
import {ref} from 'vue';
import {useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import Event from "@/Components/Event.vue";
import Swal from 'sweetalert2';

defineProps(['events']);

let form = useForm({
    id: null,
    title: '',
    description: '',
    location: '',
    start_date: '',
    end_date: '',
    is_online: false,
    organizer_name: '',
    organizer_email: '',
    max_attendees: null,
    price: 0,
    currency: 'XOF',
    status: 'draft',
    banner_image: '',
});

const showModal = ref(false);
const isEditMode = ref(false);

const openModalForCreate = () => {
    isEditMode.value = false;
    form.reset();
    form.banner_image = '';
    showModal.value = true;
};

const openModalForEdit = (event: any) => {
    isEditMode.value = true;

    form = useForm({
        id: event.id,
        title: event.title || '',
        description: event.description || '',  // Description de l'événement
        location: event.location || '',  // Lieu de l'événement
        start_date: event.start_date || '',  // Date de début
        end_date: event.end_date || '',  // Date de fin
        is_online: event.is_online || false,  // Est-ce un événement en ligne
        organizer_name: event.organizer_name || '',  // Nom de l'organisateur
        organizer_email: event.organizer_email || '',  // Email de l'organisateur
        max_attendees: event.max_attendees || null,  // Nombre max de participants
        price: event.price || 0,  // Prix de l'événement
        currency: event.currency || 'USD',  // Devise de l'événement
        status: event.status || 'draft',  // Statut de l'événement
        banner_image: event.banner_image || '',  // Bannière de l'événement
    });

    showModal.value = true;
};

const handleFileChange = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        // Ici, vous pouvez ajouter un code pour télécharger l'image si nécessaire
        form.banner_image = URL.createObjectURL(file); // Prévisualisation de l'image
    }
};

const deleteEvent = (id: number) => {
    // Utilisation de SweetAlert pour confirmation
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Cet événement sera définitivement supprimé !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer!',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            // Si l'utilisateur confirme, envoyer la requête de suppression
            form.delete(route("events.destroy", id), {
                onSuccess: () => {
                    // Mise à jour de l'état local pour retirer l'événement supprimé
                    events.value = events.value.filter((event: any) => event.id !== id);
                    Swal.fire(
                        'Supprimé!',
                        'L\'événement a été supprimé.',
                        'success'
                    );
                },
                onError: () => {
                    Swal.fire(
                        'Erreur!',
                        'Une erreur s\'est produite lors de la suppression de l\'événement.',
                        'error'
                    );
                }
            });
        }
    });
};


const submitForm = () => {
    const formData = new FormData();

    formData.append('id', form.id || '');
    formData.append('title', form.title);
    formData.append('description', form.description);
    formData.append('location', form.location);
    formData.append('start_date', form.start_date);
    formData.append('end_date', form.end_date);
    formData.append('is_online', String(form.is_online));
    formData.append('organizer_name', form.organizer_name);
    formData.append('organizer_email', form.organizer_email);
    formData.append('max_attendees', String(form.max_attendees));
    formData.append('price', String(form.price));
    formData.append('currency', form.currency);
    formData.append('status', form.status);
    if (form.banner_image) {
        formData.append('banner_image', form.banner_image);  // Ajouter l'image si elle a été modifiée
    }

    if (isEditMode.value) {
        form.patch('/events/' + form.id, formData, {
            onSuccess: () => {
                showModal.value = false;
            },
        });
    } else {
        form.post('/events', formData, {
            onSuccess: () => {
                showModal.value = false;
            },
        });
    }
};

const closeModal = () => {
    showModal.value = false;
};
</script>


<template>
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>
        <div class="flex justify-end w-full p-6">
            <!-- Bouton pour ouvrir le modal -->
            <button class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700" @click="openModalForCreate">
                Créer un nouvel événement
            </button>

        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50 bg-gray-500 bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-3xl">
                <div class="flex justify-between items-center">
                    <<h2 class="text-2xl font-semibold">{{
                        isEditMode ? 'Modifier un événement' : 'Créer un événement'
                    }}</h2>

                    <button class="text-gray-600 hover:text-gray-900" @click="closeModal">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"></path>
                        </svg>
                    </button>
                </div>

                <form class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 max-h-[60vh] overflow-y-auto overflow-hidden"
                      @submit.prevent="submitForm">
                    <!-- Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="title">Titre</label>
                        <input
                            id="title"
                            v-model="form.title"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded"
                            required
                            type="text"
                        />
                        <InputError :message="form.errors.title" class="mt-2"/>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="description">Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded"
                            rows="4"
                        ></textarea>
                        <InputError :message="form.errors.description" class="mt-2"/>
                    </div>

                    <!-- Lieu -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="location">Lieu</label>
                        <input
                            id="location"
                            v-model="form.location"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded"
                            type="text"
                        />
                        <InputError :message="form.errors.location" class="mt-2"/>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="start_date">Date de début</label>
                        <input
                            id="start_date"
                            v-model="form.start_date"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded"
                            required
                            type="datetime-local"
                        />
                        <InputError :message="form.errors.start_date" class="mt-2"/>
                    </div>

                    <!-- End Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="end_date">Date de fin</label>
                        <input
                            id="end_date"
                            v-model="form.end_date"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded"
                            type="datetime-local"
                        />
                        <InputError :message="form.errors.end_date" class="mt-2"/>
                    </div>

                    <!-- Is Online -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="is_online">Événement en ligne ?</label>
                        <input
                            id="is_online"
                            v-model="form.is_online"
                            class="mt-1"
                            type="checkbox"
                        />
                        <InputError :message="form.errors.is_online" class="mt-2"/>
                    </div>

                    <!-- Organizer Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="organizer_name">Nom de l'organisateur</label>
                        <input
                            id="organizer_name"
                            v-model="form.organizer_name"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded"
                            type="text"
                        />
                        <InputError :message="form.errors.organizer_name" class="mt-2"/>
                    </div>

                    <!-- Organizer Email -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="organizer_email">Email de l'organisateur</label>
                        <input
                            id="organizer_email"
                            v-model="form.organizer_email"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded"
                            type="email"
                        />
                        <InputError :message="form.errors.organizer_email" class="mt-2"/>
                    </div>

                    <!-- Max Attendees -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold" for="max_attendees">Nombre maximum de
                            participants</label>
                        <input
                            id="max_attendees"
                            v-model="form.max_attendees"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded"
                            type="number"
                        />
                        <InputError :message="form.errors.max_attendees" class="mt-2"/>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold">Bannière</label>
                        <div class="mb-2">
                            <!-- Affichage de l'image actuelle si elle existe -->
                            <img v-if="form.banner_image" :src="'/storage/'+form.banner_image" alt="Image de bannière"
                                 class="w-full h-auto mb-2"/>
                        </div>
                        <!-- Sélecteur de fichier pour modifier l'image -->
                        <input :disabled="isEditMode" type="file" @change="handleFileChange"/>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-4 col-span-2">
                        <button class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700" type="submit">
                            Créer l'événement
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="flex flex-wrap gap-4 mt-6 rounded-lg w-full p-4 ">
            <Event
                v-for="event in events"
                :key="event.id"
                :event="event"
                @delete="deleteEvent"
                @edit="openModalForEdit"
            >
            </Event>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Vous pouvez ajouter des styles personnalisés ici */
</style>
