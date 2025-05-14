/**
 * Vite Setup Plugin
 * 
 * This plugin completely disables HMR and file watching during setup
 * to prevent page reloads when environment variables change.
 */
export default function setupPlugin() {
    return {
        name: 'setup-disable-hmr',
        configureServer(server) {
            // Check if we're in setup mode with HMR disabled
            if (process.env.VITE_DISABLE_HMR === 'true') {
                // Override the HMR websocket handler to do nothing
                server.hot = {
                    on: () => {},
                    send: () => {}
                };
                
                // Override the watcher to prevent file change events
                const originalWatcher = server.watcher;
                server.watcher = {
                    on: (event, callback) => {
                        if (event !== 'change') {
                            return originalWatcher.on(event, callback);
                        }
                        // Don't register change handlers
                        return originalWatcher;
                    },
                    add: () => {},
                    close: () => originalWatcher.close()
                };
                
                console.log('🔒 Setup mode: HMR and file watching disabled');
            }
        }
    };
} 