from flask import Flask

app = Flask(__name__)

@app.route('/')
def index():
    return """
    <html>
    <head>
        <title>NotifyCord - Laravel Discord Notification Package</title>
        <link href="https://cdn.replit.com/agent/bootstrap-agent-dark-theme.min.css" rel="stylesheet">
    </head>
    <body data-bs-theme="dark" class="p-4">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card mt-4">
                        <div class="card-header bg-dark">
                            <h2 class="my-2 text-light">NotifyCord</h2>
                            <p class="text-light mb-0">Laravel Discord Notification Package</p>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5>Package Information</h5>
                                <p>This is a Laravel package for sending Discord notifications with rich formatting options. It's intended to be used in a Laravel environment.</p>
                            </div>
                            
                            <h4>Features</h4>
                            <ul class="list-group mb-4">
                                <li class="list-group-item">🔌 Seamless Laravel Notification System Integration</li>
                                <li class="list-group-item">🤖 Bot-based Channel & User Messaging</li>
                                <li class="list-group-item">🔗 Discord Webhook Support</li>
                                <li class="list-group-item">📋 Rich Embed Message Support</li>
                                <li class="list-group-item">🔘 Discord Component Support (Buttons, Action Rows)</li>
                                <li class="list-group-item">🔁 Rate Limit Handling with Auto-retry</li>
                                <li class="list-group-item">🧩 Queue-compatible for Async Processing</li>
                            </ul>
                            
                            <div class="alert alert-warning">
                                <h5>Note</h5>
                                <p>This application doesn't have a running demo since it requires a Laravel environment. 
                                Please refer to the package documentation and code examples for usage instructions.</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-0"><strong>GitHub:</strong> <a href="https://github.com/NotifyCord/NotifyCord" class="text-info" target="_blank">NotifyCord/NotifyCord</a></p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <p class="mb-0"><strong>License:</strong> MIT</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    """

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)