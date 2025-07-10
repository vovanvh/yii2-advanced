type Coordinates = `${number},${number}`;
const fetchedCache = new Map<Coordinates, number>();

async function fetchWeather(lat: number, lon: number): Promise<number | null> {
    const coordinatesKey: Coordinates = `${lat},${lon}`;

    if (fetchedCache.has(coordinatesKey)) {
        return fetchedCache.get(coordinatesKey)!;
    }

    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`;

    try {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`API error: ${res.statusText}`);
        const data = await res.json();
        const temperature = data?.current_weather?.temperature;

        if (typeof temperature === 'number') {
            fetchedCache.set(coordinatesKey, temperature);
            return temperature;
        }

        return null;
    } catch (err) {
        console.error('Fetch failed:', err);
        return null;
    }
}

export async function renderWeatherInElements(className: string) {
    const elements = document.querySelectorAll<HTMLElement>(`.${className}`);

    for (const el of elements) {
        const lat = parseFloat(el.dataset.lat || '');
        const lon = parseFloat(el.dataset.lon || '');

        if (isNaN(lat) || isNaN(lon)) {
            console.warn('Invalid coordinates for element:', el);
            continue;
        }

        const temp = await fetchWeather(lat, lon);

        if (temp !== null) {
            el.innerText = `🌡️ ${temp}°C`;
            el.style.display = 'block'; // Or remove a 'hidden' class if used
        } else {
            el.innerText = 'Weather data unavailable';
        }
    }
}
