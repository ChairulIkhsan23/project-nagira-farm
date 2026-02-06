import { inject, computed } from "vue";

type Theme = "light" | "dark";

interface ThemeContext {
    isDarkMode: { value: boolean };
    toggleTheme: () => void;
    theme: { value: Theme };
}

export function useTheme(): ThemeContext {
    const themeContext = inject<ThemeContext>("theme");

    if (!themeContext) {
        throw new Error("useTheme must be used within a ThemeProvider");
    }

    return themeContext;
}
