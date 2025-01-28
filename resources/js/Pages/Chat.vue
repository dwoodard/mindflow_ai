<script setup>
import { ref, computed } from 'vue'
import { VueFlow } from '@vue-flow/core'

// these components are only shown as examples of how to use a custom node or edge
// you can find many examples of how to create these custom components in the examples page of the docs
import SpecialNode from '@/components/SpecialNode.vue'
import SpecialEdge from '@/components/SpecialEdge.vue'

//add message as a reactive variable
const message = ref('think of idea of a new business')


// these are our nodes
const nodes = ref([
   
])

// these are our edges
const edges = ref([
  // default bezier edge
  // consists of an edge id, source node id and target node id
  
])

// id counter for nodes
// calculate the number of nodes and edges
const nodeCount = computed(() => nodes.value.length);
const edgeCount = computed(() => edges.value.length);
const idCounter = ref(nodeCount.value + edgeCount.value);

// send message function
const sendMessage = async () => {
    if (!message.value) return;

    // Add user message as a node
    const userNode = { id: `node-${idCounter.value++}`, type: 'default', position: { x: 100, y: 100 }, data: { label: message.value } };
    nodes.value.push(userNode);

        const system = `
        You are a JSON response generator specialized in creating flowchart data for Vue Flow. 

        Your task is to return flowchart data in a specific JSON format with two main properties: "nodes" and "edges". 

        Each "node" in the "nodes" array must have:
        - "id": A unique identifier (string).
        - "type": The type of the node (e.g., "input", "default", or "output"). If not specified, default to "default".
        - "position": An object with "x" and "y" coordinates (e.g., { "x": 100, "y": 200 }).
        - "data": An object containing any data for the node. Include a "label" property for the node's label.

        Each "edge" in the "edges" array must have:
        - "id": A unique identifier (string).
        - "source": The ID of the source node (string).
        - "target": The ID of the target node (string).
        - "animated" (optional): A boolean to indicate if the edge should be animated. Default is false.

        Your response **must only be in JSON**. Do not include any explanations or extra text.

        Example Response:
        {
            "nodes": [
                { "id": "1", "type": "input", "position": { "x": 100, "y": 100 }, "data": { "label": "Start" } },
                { "id": "2", "type": "default", "position": { "x": 200, "y": 200 }, "data": { "label": "Process" } },
                { "id": "3", "type": "output", "position": { "x": 300, "y": 300 }, "data": { "label": "End" } }
            ],
            "edges": [
                { "id": "e1->2", "source": "1", "target": "2" },
                { "id": "e2->3", "source": "2", "target": "3", "animated": true }
            ]
        } `;

        const jsonSchema = `
        {
            "nodes": [
                {
                    "id": "1",
                    "type": "input",
                    "position": { "x": 100, "y": 100 },
                    "data": { "label": "Start" }
                },
                {
                    "id": "2",
                    "type": "default",
                    "position": { "x": 200, "y": 200 },
                    "data": { "label": "Process" }
                },
                {
                    "id": "3",
                    "type": "output",
                    "position": { "x": 300, "y": 300, "width": 400 },
                    "data": { "label": "End" }
                }
            ],
            "edges": [
                { "id": "e1->2", "source": "1", "target": "2" },
                { "id": "e2->3", "source": "2", "target": "3", "animated": true }
            ]
        } `;

    
    // Send message to Laravel API
    const response = await fetch('/api/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            prompt: message.value,
            system: system,
            format: jsonSchema
        
        }),
    });
    const data = await response.json();

    // Add bot response as a node
    const botNode = { id: `node-${idCounter.value++}`, type: 'default', position: { x: 400, y: 200 }, data: { label: data.response } };
    nodes.value.push(botNode);

    // Add a link between the user message and bot response
    edges.value.push({ id: `e${userNode.id}->${botNode.id}`, source: userNode.id, target: botNode.id });

    message.value = '';
}


</script>

<template>
    <div class="flex flex-col h-screen ">
        <div class="flex items-center space-x-4 mb-4 p-4 ">
            <textarea 
            v-model="message" 
            placeholder="Enter your message" 
            class="flex-1 p-2 border rounded resize-y bg-gray-100 text-white dark:text-black"
            rows="1"
            @input="autoResize"
            @keydown="handleKeydown"
            ></textarea>

            <button 
            @click="sendMessage" 
            class="p-2 bg-blue-500 text-white dark:text-black rounded hover:bg-blue-700"
            >
            Send
            </button>
        </div>
        

        <VueFlow :nodes="nodes" :edges="edges">
            <!-- bind your custom node type to a component by using slots, slot names are always `node-<type>` -->
            <template #node-special="specialNodeProps">
                <SpecialNode v-bind="specialNodeProps" />
            </template>

            <!-- bind your custom edge type to a component by using slots, slot names are always `edge-<type>` -->
            <template #edge-special="specialEdgeProps">
                <SpecialEdge v-bind="specialEdgeProps" />
            </template>
        </VueFlow>

       
       
    </div>
</template>

<style>
    @import '@vue-flow/core/dist/style.css';
    @import '@vue-flow/core/dist/theme-default.css'; 
</style>
