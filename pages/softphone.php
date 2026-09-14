<?php
/**
 * AsteriskPBX WebRTC Softphone
 * 
 * Browser-based SIP client using SIP.js
 * User logs into their extension via browser
 */

$page_security = 'SA_ASTERISKVIEW';
$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FA_AsteriskPBX/includes/asterisk_db.inc");
include_once($path_to_root . "/modules/FA_AsteriskPBX/includes/asterisk_config.inc");

// Get user's extension
$my_extension = get_extension_for_employee($_SESSION["wa_user"]->employee_id);

$asterisk_host = get_asterisk_host();
$ws_url = "wss://{$asterisk_host}:8089/ws"; // WebSocket for WebRTC

page(_("WebRTC Phone"), false, false, get_webrtc_js());

?>
<style>
.webrtc-container {
    max-width: 800px;
    margin: 0 auto;
}
.status-bar {
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
}
.status-connected { background: #27ae60; color: white; }
.status-disconnected { background: #e74c3c; color: white; }
.status-calling { background: #f39c12; color: white; }
.phone-display {
    background: #2c3e50;
    color: white;
    padding: 20px;
    font-size: 24px;
    text-align: center;
    border-radius: 5px;
    margin: 10px 0;
}
.dialpad {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    margin: 10px 0;
}
.dialpad button {
    padding: 20px;
    font-size: 18px;
    background: #ecf0f1;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.dialpad button:hover { background: #bdc3c7; }
.dialpad button.action { background: #3498db; color: white; }
.dialpad button.hangup { background: #e74c3c; color: white; }
.call-history {
    max-height: 200px;
    overflow-y: auto;
}
</style>

<div class="webrtc-container">
    <h2><?php echo _("WebRTC Softphone"); ?></h2>
    
    <?php if (!$my_extension): ?>
        <div class="error">
            <p><?php echo _("No extension assigned to your user account."); ?></p>
            <p><?php echo _("Please contact your system administrator."); ?></p>
        </div>
    <?php else: ?>
        <div id="status-bar" class="status-bar status-disconnected">
            <?php echo _("Status: Disconnected"); ?>
        </div>
        
        <div class="phone-display" id="phone-display">
            <?php echo $my_extension['extension']; ?>
        </div>
        
        <div class="dialpad">
            <button onclick="dial('1')">1</button>
            <button onclick="dial('2')">2</button>
            <button onclick="dial('3')">3</button>
            <button onclick="dial('4')">4</button>
            <button onclick="dial('5')">5</button>
            <button onclick="dial('6')">6</button>
            <button onclick="dial('7')">7</button>
            <button onclick="dial('8')">8</button>
            <button onclick="dial('9')">9</button>
            <button onclick="dial('*')">*</button>
            <button onclick="dial('0')">0</button>
            <button onclick="dial('#')">#</button>
        </div>
        
        <div class="dialpad">
            <button class="action" onclick="dial_number = prompt('<?php echo _("Enter number:"); ?>'); if(dial_number) makeCall(dial_number)">
                <?php echo _("Dial Number"); ?>
            </button>
            <button class="hangup" onclick="hangup()">
                <?php echo _("Hang Up"); ?>
            </button>
        </div>
        
        <h3><?php echo _("Recent Calls"); ?></h3>
        <div class="call-history">
            <?php
            $history = get_recent_calls_for_extension($my_extension['extension'], 10);
            if ($history) {
                start_table(TABLESTYLE);
                table_header([_('Time'), _('From'), _('To'), _('Status')]);
                while ($call = db_fetch($history)) {
                    alt_table_row($call);
                    label_cell(substr($call['call_start'], 11, 5));
                    label_cell($call['caller_number']);
                    label_cell($call['called_number']);
                    label_cell($call['status']);
                }
                end_table(1);
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/sip.js@0.20.0/dist/sip.min.js"></script>
<script>
var userAgent = null;
var currentCall = null;
var dialedNumber = '';
var wsUrl = '<?php echo $ws_url; ?>';

function get_webrtc_js() {
    return '';
}

function updateStatus(status, text) {
    var bar = document.getElementById('status-bar');
    var display = document.getElementById('phone-display');
    
    bar.className = 'status-bar status-' + status;
    bar.innerHTML = text;
    
    if (status === 'calling') {
        display.innerHTML = dialedNumber || 'Calling...';
    } else if (status === 'connected') {
        display.innerHTML = 'In Call: ' + dialedNumber;
    }
}

function initSIP() {
    var extension = '<?php echo $my_extension['extension'] ?? ''; ?>';
    var password = '<?php echo $my_extension['sip_peer'] ?? 'changeme'; ?>';
    
    if (!extension) return;
    
    try {
        userAgent = new SIP.UserAgent({
            uri: SIP.UserAgent.makeURI('sip:' + extension + '@<?php echo $asterisk_host; ?>'),
            transportFactory: new SIP.WebSocketTransportFactory({
                wsUri: wsUrl
            }),
            authorizationUsername: extension,
            authorizationPassword: password,
            sessionDescriptionHandlerFactoryOptions: {
                peerConnectionOptions: {
                    iceServers: [{urls: 'stun:stun.l.google.com:19302'}]
                }
            }
        });
        
        userAgent.delegate = {
            onInvite: function(invitation) {
                handleIncomingCall(invitation);
            }
        };
        
        userAgent.start().then(function() {
            updateStatus('connected', '<?php echo _("Status: Connected"); ?>');
        }).catch(function(e) {
            updateStatus('disconnected', '<?php echo _("Status: Connection Failed"); ?>: ' + e);
        });
    } catch(e) {
        updateStatus('disconnected', '<?php echo _("Status: Error"); ?>: ' + e);
    }
}

function handleIncomingCall(invitation) {
    var caller = invitation.remoteIdentity.displayName || invitation.remoteIdentity.uri.user;
    var accept = confirm('<?php echo _("Incoming call from:"); ?> ' + caller + '\n\n<?php echo _("Accept?"); ?>');
    
    if (accept) {
        invitation.accept().then(function(session) {
            currentCall = session;
            dialedNumber = caller;
            updateStatus('connected', '<?php echo _("In Call"); ?>: ' + caller);
            
            session.stateChange.addListener(SIP.SessionState.HangupReceived, function() {
                updateStatus('connected', '<?php echo _("Call Ended"); ?>');
                currentCall = null;
            });
        });
    } else {
        invitation.reject();
    }
}

function makeCall(number) {
    if (!userAgent) {
        alert('<?php echo _("Not connected. Please refresh."); ?>');
        return;
    }
    
    dialedNumber = number;
    updateStatus('calling', '<?php echo _("Calling:"); ?> ' + number);
    
    var target = SIP.UserAgent.makeURI('sip:' + number + '@<?php echo $asterisk_host; ?>');
    
    userAgent.invite(target, {
        sessionDescriptionHandlerOptions: {
            offerToSendAudio: true,
            offerToSendVideo: false
        }
    }).then(function(session) {
        currentCall = session;
        updateStatus('connected', '<?php echo _("In Call"); ?>: ' + number);
        
        session.stateChange.addListener(SIP.SessionState.HangupReceived, function() {
            updateStatus('connected', '<?php echo _("Call Ended"); ?>');
            currentCall = null;
            dialedNumber = '';
        });
    }).catch(function(e) {
        updateStatus('disconnected', '<?php echo _("Call Failed:"); ?> ' + e);
    });
}

function dial(digit) {
    dialedNumber += digit;
    document.getElementById('phone-display').innerHTML = dialedNumber;
    
    if (currentCall && currentCall.logger) {
        currentCall.sessionDescriptionHandler.sendDtmf(digit);
    }
}

function hangup() {
    if (currentCall) {
        currentCall.bye();
        currentCall = null;
    }
    dialedNumber = '';
    updateStatus('connected', '<?php echo _("Status: Connected"); ?>');
}

// Auto-connect when page loads
<?php if ($my_extension): ?>
initSIP();
<?php endif; ?>
</script>

<?php 
end_page(true);