<template>
    <div>
        <div class="flex items-center space-x-4 mb-4 p-4 ">
            <textarea 
            v-model="message" 
            placeholder="Enter your message" 
            class="flex-1 p-2 border rounded resize-y bg-gray-100"
            rows="1"
            @input="autoResize"
            @keydown="handleKeydown"
            ></textarea>
            <button 
            @click="sendMessage" 
            class="p-2 bg-blue-500 text-white rounded hover:bg-blue-700"
            >
            Send
            </button>
        </div>
        
        <svg ref="svg" 
            class="w-full h-96"
        ></svg>
       
    </div>
</template>

<script>
import * as d3 from 'd3';

export default {
    data() {
        return {
            nodes: [],
            links: [],
            message: '',
            idCounter: 1,
        };
    },
    methods: {
        drawGraph() {
            const svg = d3.select(this.$refs.svg);
            svg.selectAll('*').remove(); // Clear the SVG before re-rendering

            const simulation = d3.forceSimulation(this.nodes)
                .force('link', d3.forceLink(this.links).id(d => d.id).distance(150).strength(0.1))
                .force('charge', d3.forceManyBody().strength(-200))
                .force('center', d3.forceCenter(this.$refs.svg.clientWidth / 2, this.$refs.svg.clientHeight / 2))
                .force('collision', d3.forceCollide().radius(50));

            const link = svg.selectAll('line')
                .data(this.links)
                .enter()
                .append('line')
                .attr('stroke', '#999');

            const node = svg.selectAll('circle')
                .data(this.nodes)
                .enter()
                .append('circle')
                .attr('r', 10)
                .attr('fill', '#69b3a2')
                .call(drag(simulation));

            const text = svg.selectAll('text')
                .data(this.nodes)
                .enter()
                .append('text')
                .text(d => d.label)
                .attr('font-size', 12)
                .attr('dx', 15)
                .attr('dy', 4);

            simulation.on('tick', () => {
                link
                    .attr('x1', d => d.source.x)
                    .attr('y1', d => d.source.y)
                    .attr('x2', d => d.target.x)
                    .attr('y2', d => d.target.y);

                node
                    .attr('cx', d => d.x)
                    .attr('cy', d => d.y);

                text
                    .attr('x', d => d.x)
                    .attr('y', d => d.y);
            });

            function drag(simulation) {
                function dragstarted(event, d) {
                    if (!event.active) simulation.alphaTarget(0.3).restart();
                    d.fx = d.x;
                    d.fy = d.y;
                }

                function dragged(event, d) {
                    d.fx = event.x;
                    d.fy = event.y;
                }

                function dragended(event, d) {
                    if (!event.active) simulation.alphaTarget(0);
                    d.fx = null;
                    d.fy = null;
                }

                return d3.drag()
                    .on('start', dragstarted)
                    .on('drag', dragged)
                    .on('end', dragended);
            }
        },
        async sendMessage() {
            if (!this.message) return;

            // Add user message as a node
            const userNode = { id: `node-${this.idCounter++}`, label: this.message };
            this.nodes.push(userNode);

            // Send message to Laravel API
            const response = await fetch('/api/chat', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ prompt: this.message }),
            });
            const data = await response.json();

            // Add bot response as a node
            const botNode = { id: `node-${this.idCounter++}`, label: data.response };
            this.nodes.push(botNode);

            // Add a link between the user message and bot response
            this.links.push({ source: userNode.id, target: botNode.id });

            this.message = '';
            this.drawGraph();
        },
        autoResize(event) {
            event.target.style.height = 'auto';
            event.target.style.height = `${event.target.scrollHeight}px`;
        },
        handleKeydown(event) {
            if (event.metaKey && event.key === 'Enter') {
                this.sendMessage();
            }
        }
    },
    mounted() {
        this.drawGraph();
    },
};
</script>

<style>
svg {
    border: 1px solid #ccc;
}
</style>
