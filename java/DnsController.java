@RestController
@RequestMapping("/api/dns")
public class DnsController {
    @Autowired private DnsService dnsService;

    @PostMapping("/add")
    public String add(@RequestParam String domain, @RequestParam String ip) {
        try {
            dnsService.addDnsRecord(domain, ip);
            return "DNS ajouté !";
        } catch (Exception e) { return "Erreur : " + e.getMessage(); }
    }
}