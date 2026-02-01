@Service
public class DnsService {
    // Cette methode ecrit dans le fichier de zone du serveur DNS (ex: BIND9)
    public void addDnsRecord(String domain, String ip) throws IOException {
        String record = domain + " IN A " + ip;
        Files.write(Paths.get("/etc/bind/zones/db.cloud"), (record + "\n").getBytes(), StandardOpenOption.APPEND);
        
        // Recharge la configuration DNS
        Runtime.getRuntime().exec("rndc reload");
    }
}