<script setup>
import { ref } from 'vue'

const activeChat = ref(0)
const newMessage = ref('')

const conversations = ref([
  { id: 1, host: 'Sofia (Villa Ocean Breeze)', date: '12:05', unread: 2, excerpt: 'Bonjour, l\'arrivée tardive est possible ?' },
  { id: 2, host: 'Marc (Casa Palmera)', date: 'Hier', unread: 0, excerpt: 'La vue est incroyable.' },
  { id: 3, host: 'Équipe Villa Project', date: 'Lun', unread: 0, excerpt: 'Votre séjour a été confirmé.' },
])

const messages = ref([
  { id: 1, sender: 'me', text: "Bonjour Sofia, nous serons ravis de séjourner dans votre villa. Serait-il possible de faire une arrivée tardive vers 22h30 ?", time: "11:45" },
  { id: 2, sender: 'them', text: "Bonjour ! Oui tout à fait, nous avons une boîte à clés sécurisée. Je vous donnerai le code le jour J.", time: "12:02" },
  { id: 3, sender: 'me', text: "Parfait, merci beaucoup !", time: "12:05", status: "read" },
])

const sendMessage = () => {
  if (!newMessage.value.trim()) return
  messages.value.push({
    id: Date.now(),
    sender: 'me',
    text: newMessage.value.trim(),
    time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' }),
    status: 'sent'
  })
  newMessage.value = ''
  
  // Simulation de réponse
  setTimeout(() => {
    messages.value.push({
      id: Date.now() + 1,
      sender: 'them',
      text: "C'est noté. Si vous avez d'autres questions, n'hésitez pas.",
      time: new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
    })
  }, 1500)
}
</script>

<template>
  <v-container class="py-8 fill-height align-start" style="max-width: 1200px;">
    <div class="mb-6 w-100">
      <h1 class="text-h4 font-weight-bold text-primary mb-2">Messagerie</h1>
      <p class="text-body-1 text-medium-emphasis">
        Échangez avec les hôtes, réglez les détails de votre séjour et posez vos questions.
      </p>
    </div>

    <!-- Chat Card -->
    <v-card class="elevation-3 rounded-xl w-100 d-flex overflow-hidden" height="70vh" style="min-height: 500px;">
      <!-- Sidebar / Liste des conversations -->
      <div class="bg-grey-lighten-4 border-e border-r" style="width: 320px; flex-shrink: 0; display: flex; flex-direction: column;">
        <div class="pa-4 bg-white elevation-1 z-1">
          <v-text-field
            placeholder="Rechercher"
            prepend-inner-icon="mdi-magnify"
            variant="solo-filled"
            density="compact"
            hide-details
            rounded="pill"
            bg-color="grey-lighten-3"
          ></v-text-field>
        </div>

        <v-list class="bg-transparent flex-grow-1 overflow-y-auto pt-2" lines="two">
          <v-list-item
            v-for="(conv, i) in conversations"
            :key="conv.id"
            :active="activeChat === i"
            @click="activeChat = i"
            active-color="primary"
            class="px-4 py-3 mx-2 mb-2 rounded-lg cursor-pointer"
            :class="{ 'bg-white elevation-1': activeChat === i, 'hover-lighter': activeChat !== i }"
          >
            <template #prepend>
              <v-avatar color="primary-lighten-3" size="48">
                <span class="text-primary font-weight-black text-h6">{{ conv.host.charAt(0) }}</span>
              </v-avatar>
            </template>
            <v-list-item-title class="font-weight-bold d-flex justify-space-between text-body-1">
              {{ conv.host.split(' ')[0] }}
              <span class="text-caption text-medium-emphasis font-weight-regular">{{ conv.date }}</span>
            </v-list-item-title>
            <v-list-item-subtitle class="text-caption mt-1 d-flex justify-space-between align-center">
              <span class="text-truncate mr-2" style="max-width: 160px;">{{ conv.excerpt }}</span>
              <v-badge
                v-if="conv.unread > 0"
                color="error"
                :content="conv.unread"
                inline
              ></v-badge>
            </v-list-item-subtitle>
          </v-list-item>
        </v-list>
      </div>

      <!-- Main Chat Area -->
      <div class="flex-grow-1 bg-grey-lighten-5 d-flex flex-column h-100 relative">
        <!-- Chat Header -->
        <v-toolbar color="white" elevation="1" class="px-2" height="72">
          <v-avatar color="primary-lighten-3" size="42" class="mr-3">
            <span class="text-primary font-weight-black text-h6">{{ conversations[activeChat].host.charAt(0) }}</span>
          </v-avatar>
          <div>
            <h2 class="text-subtitle-1 font-weight-bold mb-0 leading-tight">{{ conversations[activeChat].host }}</h2>
            <span class="text-caption text-success font-weight-medium">En ligne</span>
          </div>
          <v-spacer></v-spacer>
          <v-btn icon color="primary" variant="text"><v-icon>mdi-phone-outline</v-icon></v-btn>
          <v-btn icon color="grey-darken-1" variant="text"><v-icon>mdi-dots-vertical</v-icon></v-btn>
        </v-toolbar>

        <!-- Messages Area -->
        <div class="flex-grow-1 overflow-y-auto pa-6 d-flex flex-column gap-3 chat-bg">
          <div class="text-center mb-4">
            <v-chip size="small" variant="flat" color="grey-lighten-2" class="text-medium-emphasis">Aujourd'hui</v-chip>
          </div>

          <div
            v-for="msg in messages" 
            :key="msg.id"
            class="d-flex w-100"
            :class="msg.sender === 'me' ? 'justify-end' : 'justify-start'"
          >
            <div 
              class="message-bubble pa-3 rounded-lg elevation-1"
              :class="msg.sender === 'me' ? 'bg-primary text-white rounded-br-0' : 'bg-white rounded-bl-0'"
              style="max-width: 70%;"
            >
              <div class="text-body-1 mb-1">{{ msg.text }}</div>
              <div 
                class="text-caption text-right d-flex align-center justify-end font-weight-medium"
                :class="msg.sender === 'me' ? 'text-white text-opacity-80' : 'text-medium-emphasis'"
              >
                {{ msg.time }}
                <v-icon v-if="msg.sender === 'me'" size="14" class="ml-1" :color="msg.status === 'read' ? 'cyan-accent-1' : 'white'">mdi-check-all</v-icon>
              </div>
            </div>
          </div>
        </div>

        <!-- Input Area -->
        <div class="pa-4 bg-white border-t d-flex align-end elevation-1">
          <v-btn icon ariant="text" color="grey-darken-1" class="mr-2 mb-1"><v-icon>mdi-paperclip</v-icon></v-btn>
          <v-textarea
            v-model="newMessage"
            placeholder="Écrivez votre message..."
            variant="solo-filled"
            bg-color="grey-lighten-4"
            flat
            hide-details
            auto-grow
            rows="1"
            max-rows="4"
            class="mr-2"
            @keydown.enter.exact.prevent="sendMessage"
          ></v-textarea>
          <v-btn 
            fab 
            color="primary" 
            elevation="2" 
            height="56" 
            width="56" 
            min-width="56" 
            class="rounded-circle align-self-center ml-1"
            @click="sendMessage"
            :disabled="!newMessage.trim()"
          >
            <v-icon size="28">mdi-send</v-icon>
          </v-btn>
        </div>
      </div>
    </v-card>
  </v-container>
</template>

<style scoped>
.chat-bg {
  background-image: url('data:image/svg+xml,%3Csvg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%23e0e0e0" fill-opacity="0.4" fill-rule="evenodd"%3E%3Ccircle cx="3" cy="3" r="3"/%3E%3Ccircle cx="13" cy="13" r="3"/%3E%3C/g%3E%3C/svg%3E');
}
.hover-lighter:hover {
  background-color: rgba(200, 200, 200, 0.1) !important;
}
.gap-3 {
  gap: 12px;
}
.text-opacity-80 {
  opacity: 0.8;
}
.rounded-bl-0 { border-bottom-left-radius: 0 !important; }
.border-e { border-right: 1px solid rgba(0,0,0,0.08); }
.border-t { border-top: 1px solid rgba(0,0,0,0.05); }
.z-1 { z-index: 1; position: relative }
</style>
