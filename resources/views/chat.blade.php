<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chatbot with D3</title>
    <script src="https://d3js.org/d3.v7.min.js"></script>

    <style>
        body { font-family: Arial, sans-serif; }
        .bubble { cursor: pointer; }
    </style>
</head>
<body>
    <h1>Interactive Chatbot</h1>
    <div id="chat-container"></div>

    <script>
        const container = d3.select('#chat-container');
        const apiUrl = 'http://localhost:8000/api/chat';

        const chatData = [];

        function addMessage(type, text) {
            chatData.push({ type, text });
            renderChat();
        }

        function renderChat() {
            const messages = container.selectAll('.message').data(chatData, d => d.text);

            // Enter new messages
            messages.enter()
                .append('div')
                .attr('class', d => `message ${d.type}`)
                .style('margin', '10px 0')
                .style('padding', '10px')
                .style('background-color', d => d.type === 'user' ? '#d1ecf1' : '#f8d7da')
                .text(d => d.text);

            // Remove old messages
            messages.exit().remove();
        }

        function sendMessage(userMessage) {
            addMessage('user', userMessage);

            // API call
            fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userMessage }),
            })
                .then(res => res.json())
                .then(data => {
                    const botMessage = data.response || 'Error fetching response';
                    addMessage('bot', botMessage);
                })
                .catch(() => {
                    addMessage('bot', 'Failed to connect to the chatbot.');
                });
        }

        // Example usage
        sendMessage('Hello!');
    </script>
</body>
</html>
