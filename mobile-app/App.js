import { StatusBar } from 'expo-status-bar';
import { useCallback, useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Pressable,
  SafeAreaView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import MapView, { Marker } from 'react-native-maps';

// Change this to your deployed CI4 URL in production.
// For a physical phone, use your Mac's Wi-Fi IP — never 127.0.0.1.
const API_BASE_URL = 'http://192.168.88.14:8080/api';
const BUS_ID = 1;
const REFRESH_INTERVAL = 10000;

const fallbackRegion = {
  latitude: -3.336045,
  longitude: 37.320446,
  latitudeDelta: 0.03,
  longitudeDelta: 0.03,
};

export default function App() {
  const [bus, setBus] = useState(null);
  const [location, setLocation] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');
  const [lastUpdated, setLastUpdated] = useState(null);

  const loadLocation = useCallback(async () => {
    try {
      setError('');

      const response = await fetch(`${API_BASE_URL}/buses/${BUS_ID}/location`);
      const payload = await response.json();

      if (!response.ok) {
        throw new Error(payload.message ?? 'Unable to load the bus location.');
      }

      setBus(payload.data.bus);
      setLocation(payload.data.location);
      setLastUpdated(new Date());
    } catch (requestError) {
      setError(requestError.message ?? 'Unable to connect to the tracking server.');
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    loadLocation();

    const interval = setInterval(loadLocation, REFRESH_INTERVAL);

    return () => clearInterval(interval);
  }, [loadLocation]);

  const region = location
    ? {
        latitude: location.latitude,
        longitude: location.longitude,
        latitudeDelta: 0.01,
        longitudeDelta: 0.01,
      }
    : fallbackRegion;

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar style="dark" />
      <View style={styles.header}>
        <View>
          <Text style={styles.eyebrow}>LIVE BUS TRACKING</Text>
          <Text style={styles.title}>{bus?.registration_number ?? 'School Bus'}</Text>
        </View>
        <Pressable style={styles.refreshButton} onPress={loadLocation}>
          <Text style={styles.refreshText}>Refresh</Text>
        </Pressable>
      </View>

      <MapView style={styles.map} region={region}>
        {location && (
          <Marker
            coordinate={{ latitude: location.latitude, longitude: location.longitude }}
            title={bus?.registration_number ?? 'School Bus'}
            description={`Speed: ${location.speed_kmh ?? 0} km/h`}
            pinColor="#0f766e"
          />
        )}
      </MapView>

      <View style={styles.panel}>
        {isLoading ? (
          <ActivityIndicator color="#0f766e" />
        ) : error ? (
          <Text style={styles.error}>{error}</Text>
        ) : location ? (
          <>
            <Text style={styles.status}>Bus is reporting live location</Text>
            <Text style={styles.coordinates}>
              {location.latitude.toFixed(6)}, {location.longitude.toFixed(6)}
            </Text>
            <Text style={styles.detail}>Speed: {location.speed_kmh ?? 0} km/h</Text>
            <Text style={styles.detail}>GPS time: {location.recorded_at}</Text>
          </>
        ) : (
          <Text style={styles.error}>No GPS location has been received yet.</Text>
        )}
        {lastUpdated && <Text style={styles.updated}>Updated: {lastUpdated.toLocaleTimeString()}</Text>}
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#f8fafc' },
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: 20 },
  eyebrow: { color: '#0f766e', fontSize: 12, fontWeight: '800', letterSpacing: 1 },
  title: { color: '#0f172a', fontSize: 24, fontWeight: '800', marginTop: 3 },
  refreshButton: { backgroundColor: '#0f766e', borderRadius: 8, paddingHorizontal: 14, paddingVertical: 10 },
  refreshText: { color: '#fff', fontWeight: '700' },
  map: { flex: 1 },
  panel: { padding: 20, backgroundColor: '#fff', minHeight: 155 },
  status: { color: '#166534', fontWeight: '800', fontSize: 16 },
  coordinates: { color: '#0f172a', fontSize: 18, fontWeight: '700', marginTop: 8 },
  detail: { color: '#475569', marginTop: 6 },
  updated: { color: '#64748b', fontSize: 12, marginTop: 10 },
  error: { color: '#b91c1c', fontWeight: '600', lineHeight: 20 },
});
