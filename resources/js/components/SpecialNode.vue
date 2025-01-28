<script setup>
import { computed } from 'vue'
import { Position, Handle } from '@vue-flow/core'

const props = defineProps({
  position: {
    type: Object,
    required: true,
  },
  data: {
    type: Object,
    default: () => ({ label: 'Fallback Label' }),
  }
})

const x = computed(() => `${Math.round(props.position.x)}px`)
const y = computed(() => `${Math.round(props.position.y)}px`)
</script>

<template>
  <div class="vue-flow__node-default">
    <h3>{{ props.data?.label || 'Fallback Label' }}</h3>

    <div>
      {x} {y}
    </div>

    <Handle type="source" :position="Position.Bottom" />
  </div>
</template>