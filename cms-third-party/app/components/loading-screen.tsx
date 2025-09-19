import { Loader } from 'lucide-react';
export default function LoadingScreen() {
    return (
        <div className="flex h-screen w-screen flex-col items-center justify-center space-y-4 bg-gray-50">
            <Loader className="animate-spin" />
            <p className="text-lg text-gray-700">Loading...</p>
        </div>
    );
}
