import react from "@vitejs/plugin-react";
import { resolve } from "path";
import { defineConfig } from "vite";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [react()],
    base: "./",
    build: {
        manifest: true,
        rollupOptions: {
            input: {
                admin: resolve(__dirname, "src", "admin", "index.tsx"),
                google: resolve(__dirname, "src", "google", "index.ts"),
            },
        },
    },
    resolve: {
        alias: {
            "@": resolve(__dirname, "./src"),
        },
    },
});
