export const isClient = typeof window !== "undefined";

export const getWindowWidth = () => {
    return isClient ? window.innerWidth : 1024;
};

export const getWindowHeight = () => {
    return isClient ? window.innerHeight : 768;
};
