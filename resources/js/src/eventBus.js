// src/eventBus.js
import { ref } from 'vue'

// Create a reactive bus
const bus = ref(new Map())

export const eventBus = {
    // Listen to an event
    on(event, callback) {
        if (!bus.value.has(event)) {
            bus.value.set(event, new Set())
        }
        bus.value.get(event).add(callback)
    },

    // Remove event listener
    off(event, callback) {
        if (bus.value.has(event)) {
            bus.value.get(event).delete(callback)
        }
    },

    // Emit an event
    emit(event, data) {
        if (bus.value.has(event)) {
            bus.value.get(event).forEach(callback => {
                callback(data)
            })
        }
    },

    // Remove all listeners for an event
    clear(event) {
        if (bus.value.has(event)) {
            bus.value.delete(event)
        }
    }
}

export default eventBus