<?php

namespace App\Core\AI\Providers;

use App\Core\AI\Contracts\AIProviderInterface;
use App\Core\AI\DTOs\AIResponse;
use Illuminate\Support\Facades\Log;

class LogLocalAiProvider implements AIProviderInterface
{
    public function getProviderName(): string
    {
        return 'local_heuristic';
    }

    public function generateCompletion(string $prompt, array $options = []): AIResponse
    {
        Log::info("Local Heuristic AI Provider processing prompt: " . substr($prompt, 0, 100));

        $capability = $options['capability'] ?? 'general';
        $context = $options['context'] ?? [];
        $reqLang = $context['language'] ?? $options['locale'] ?? app()->getLocale();

        $isHindi = $reqLang === 'hi' || (preg_match('/[\x{0900}-\x{097F}]/u', $prompt) && (str_contains(mb_strtolower($prompt), 'hindi') || str_contains($prompt, 'हिंदी')));
        $isMarathi = $reqLang === 'mr' || (preg_match('/[\x{0900}-\x{097F}]/u', $prompt) && ! $isHindi);

        if ($isHindi) {
            $responseText = $this->buildHindiResponse($prompt, $capability, $context);
        } elseif ($isMarathi) {
            $responseText = $this->buildMarathiResponse($prompt, $capability, $context);
        } else {
            $responseText = $this->buildHeuristicResponse($prompt, $capability, $context);
        }

        $suggestedActions = $this->buildSuggestedActions($capability, $context, $isMarathi);

        $promptTokens = max(20, (int) ceil(strlen($prompt) / 4));
        $completionTokens = max(40, (int) ceil(strlen($responseText) / 4));

        return AIResponse::makeSuccess(
            $responseText,
            $promptTokens,
            $completionTokens,
            $suggestedActions
        );
    }

    protected function buildHeuristicResponse(string $prompt, string $capability, array $context): string
    {
        return match ($capability) {
            'parent_communication_generator' => $this->parentCommunicationGenerator($prompt, $context),
            'lead_history_summary' => $this->leadHistorySummary($prompt, $context),
            'lead_followup_suggestions' => $this->leadFollowupSuggestions($prompt, $context),
            'draft_personalized_message' => $this->draftPersonalizedMessage($prompt, $context),
            'overdue_followups_identifier' => $this->overdueFollowupsIdentifier($prompt, $context),
            'enquiry_conversation_summary' => $this->enquiryConversationSummary($prompt, $context),
            'counselor_call_notes' => $this->counselorCallNotes($prompt, $context),
            'visit_summaries' => $this->visitSummaries($prompt, $context),
            'next_admin_actions' => $this->nextAdminActions($prompt, $context),
            'student_summary_generator' => $this->summarizeStudent($prompt, $context),
            'admission_lead_assistant' => $this->assistAdmissionLead($prompt, $context),
            'communication_generator' => $this->generateCommunication($prompt, $context),
            'followup_assistant' => $this->assistFollowup($prompt, $context),
            'parent_communication_assistant' => $this->assistParentCommunication($prompt, $context),
            'management_query_assistant' => $this->assistManagementQuery($prompt, $context),
            default => $this->generalSchoolAssistant($prompt, $context),
        };
    }

    protected function buildMarathiResponse(string $prompt, string $capability, array $context): string
    {
        return match ($capability) {
            'parent_communication_generator' => $this->parentCommunicationGeneratorMarathi($prompt, $context),
            'lead_history_summary' => $this->leadHistorySummaryMarathi($prompt, $context),
            'lead_followup_suggestions' => $this->leadFollowupSuggestionsMarathi($prompt, $context),
            'draft_personalized_message' => $this->draftPersonalizedMessageMarathi($prompt, $context),
            'overdue_followups_identifier' => $this->overdueFollowupsIdentifierMarathi($prompt, $context),
            'enquiry_conversation_summary' => $this->enquiryConversationSummaryMarathi($prompt, $context),
            'counselor_call_notes' => $this->counselorCallNotesMarathi($prompt, $context),
            'visit_summaries' => $this->visitSummariesMarathi($prompt, $context),
            'next_admin_actions' => $this->nextAdminActionsMarathi($prompt, $context),
            'student_summary_generator' => $this->summarizeStudentMarathi($prompt, $context),
            'admission_lead_assistant' => $this->assistAdmissionLeadMarathi($prompt, $context),
            'communication_generator' => $this->generateCommunicationMarathi($prompt, $context),
            'followup_assistant' => $this->assistFollowupMarathi($prompt, $context),
            'parent_communication_assistant' => $this->assistParentCommunicationMarathi($prompt, $context),
            'management_query_assistant' => $this->assistManagementQueryMarathi($prompt, $context),
            default => $this->generalSchoolAssistantMarathi($prompt, $context),
        };
    }

    protected function buildHindiResponse(string $prompt, string $capability, array $context): string
    {
        return match ($capability) {
            'parent_communication_generator' => $this->parentCommunicationGeneratorHindi($prompt, $context),
            'parent_communication_assistant' => $this->parentCommunicationGeneratorHindi($prompt, $context),
            'communication_generator' => $this->parentCommunicationGeneratorHindi($prompt, $context),
            default => $this->parentCommunicationGeneratorHindi($prompt, $context),
        };
    }

    /* -------------------------- English Responses -------------------------- */

    protected function leadHistorySummary(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'Parent';
        $studentName = $context['student_name'] ?? 'Applicant';
        $stage = strtoupper(str_replace('_', ' ', $context['stage'] ?? 'Inquiry'));
        $enquiriesCount = $context['enquiries_count'] ?? 1;
        $followupsCount = $context['followups_count'] ?? 1;

        return "### 📋 Lead Interaction History Digest\n\n" .
               "**Reason/Context**: Summarize recent applicant communications and pipeline milestones to understand engagement level.\n\n" .
               "**Suggested Action**: Review current engagement timeline and evaluate readiness for campus counseling.\n\n" .
               "**Generated Content**:\n" .
               "- **Applicant**: {$studentName} (Parent: {$parentName})\n" .
               "- **Pipeline Milestone**: Stage **{$stage}**\n" .
               "- **Engagement History**: Total {$enquiriesCount} inquiries logged and {$followupsCount} follow-up interactions recorded.\n" .
               "- **Key Highlight**: High intent displayed during initial interaction regarding STEM curriculum and school bus transport options.\n\n" .
               "**User Confirmation**: [To be reviewed, verified, and confirmed by admission staff]";
    }

    protected function leadFollowupSuggestions(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'Parent';
        $studentName = $context['student_name'] ?? 'Applicant';
        $stage = strtoupper(str_replace('_', ' ', $context['stage'] ?? 'Inquiry'));

        return "### 💡 Strategic Follow-Up Recommendation\n\n" .
               "**Reason/Context**: Analyze lead response velocity and pipeline stage to determine optimal engagement timing.\n\n" .
               "**Suggested Action**: Schedule a 15-minute phone counseling session within 24 hours.\n\n" .
               "**Generated Content**:\n" .
               "- **Lead Name**: {$parentName} (Child: {$studentName})\n" .
               "- **Current Stage**: {$stage}\n" .
               "- **Recommended Action**: Outreach via Phone / WhatsApp to invite family for a personalized campus walk-through.\n" .
               "- **Talking Points**: Highlight Grade-level faculty achievements, upcoming open house, and scholarship application deadlines.\n\n" .
               "**User Confirmation**: [To be reviewed and confirmed by admission counselor before scheduling]";
    }

    protected function draftPersonalizedMessage(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'Parent';
        $studentName = $context['student_name'] ?? 'Applicant';

        return "### ✉️ Tailored Admission Outreach Draft\n\n" .
               "**Reason/Context**: Compose customized communication draft incorporating applicant academic background.\n\n" .
               "**Suggested Action**: Review draft text in broadcast composer and dispatch to parent.\n\n" .
               "**Generated Content**:\n" .
               "\"Dear {$parentName},\n\n" .
               "Thank you for your inquiry regarding {$studentName}'s admission to SmartSchool Academy. We are thrilled about the prospect of welcoming {$studentName} to our learning community.\n\n" .
               "Our admissions office has set aside dedicated counseling slots this week. Would you be available for a short 15-minute campus visit?\n\n" .
               "Warm regards,\n" .
               "Admission Office, SmartSchool Academy\"\n\n" .
               "**User Confirmation**: [To be reviewed, edited, and approved by staff prior to sending]";
    }

    protected function overdueFollowupsIdentifier(string $prompt, array $context): string
    {
        $overdueCount = $context['overdue_followups_count'] ?? 1;
        $parentName = $context['parent_name'] ?? 'Lead';

        return "### ⏳ Overdue Action Items & Pipeline Risk Alert\n\n" .
               "**Reason/Context**: Highlight overdue tasks requiring urgent staff attention to prevent lead abandonment.\n\n" .
               "**Suggested Action**: Execute high-priority phone call to {$parentName} immediately.\n\n" .
               "**Generated Content**:\n" .
               "- **Overdue Tasks Found**: {$overdueCount} pending follow-up task(s) past SLA deadline.\n" .
               "- **Risk Assessment**: Moderate drop-off risk if non-responsive beyond 48 hours.\n" .
               "- **Action Priority**: Contact parent to confirm document receipt and resolve outstanding fee queries.\n\n" .
               "**User Confirmation**: [To be reviewed and actioned by assigned counselor]";
    }

    protected function enquiryConversationSummary(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'Parent';
        $question = $context['latest_enquiry_question'] ?? 'Inquired about fee structure and transport facilities.';

        return "### 💬 Inquiry Conversation Key Points\n\n" .
               "**Reason/Context**: Condense parent inquiry threads into key interest areas and concerns.\n\n" .
               "**Suggested Action**: Send official prospectus and fee structure breakdown PDF to parent.\n\n" .
               "**Generated Content**:\n" .
               "- **Parent Concern**: \"{$question}\"\n" .
               "- **Primary Interest**: Academic curriculum rigor, lab facilities, and bus routing.\n" .
               "- **Key takeaway**: Parent is comparing 2 peer institutions; quick response on transport fee will secure commitment.\n\n" .
               "**User Confirmation**: [To be reviewed and validated by counselor]";
    }

    protected function counselorCallNotes(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'Parent';
        $studentName = $context['student_name'] ?? 'Applicant';

        return "### 📞 Counselor Call Notes & Summary\n\n" .
               "**Reason/Context**: Produce structured call debrief post-counselor conversation with guardian.\n\n" .
               "**Suggested Action**: Save call log to Lead 360 history and trigger document checklist request.\n\n" .
               "**Generated Content**:\n" .
               "- **Call Participant**: {$parentName} (Parent of {$studentName})\n" .
               "- **Discussion Summary**: Counselor answered questions regarding lunch programs, foreign language electives, and term dates.\n" .
               "- **Parent Sentiment**: Very positive and enthusiastic about enrolling.\n" .
               "- **Agreed Next Step**: Parent will submit birth certificate and previous report card by Friday.\n\n" .
               "**User Confirmation**: [To be verified, edited, and approved by counselor for record keeping]";
    }

    protected function visitSummaries(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'Parent';
        $studentName = $context['student_name'] ?? 'Applicant';

        return "### 🏫 Campus Visit Debrief & Impressions\n\n" .
               "**Reason/Context**: Summarize campus tour debrief capturing student impressions and staff observations.\n\n" .
               "**Suggested Action**: Issue admission application link and schedule principal interaction.\n\n" .
               "**Generated Content**:\n" .
               "- **Visitor**: {$parentName} & {$studentName}\n" .
               "- **Tour Highlights**: Toured robotics lab, sports complex, and primary wing classroom.\n" .
               "- **Student Impression**: {$studentName} was particularly impressed by the science innovation lab.\n" .
               "- **Counselor Note**: Family is ready to complete formal application pending financial aid review.\n\n" .
               "**User Confirmation**: [To be reviewed and signed off by admissions officer]";
    }

    protected function nextAdminActions(string $prompt, array $context): string
    {
        $studentName = $context['student_name'] ?? 'Applicant';
        $docCount = $context['documents_count'] ?? 0;
        $appStage = strtoupper($context['application_stage'] ?? 'DRAFT');

        return "### ⚙️ Recommended Administrative Workflow Actions\n\n" .
               "**Reason/Context**: Evaluate configurable workflow rules (document completion, approval milestones, fee status).\n\n" .
               "**Suggested Action**: Request missing document uploads and generate application review ticket.\n\n" .
               "**Generated Content**:\n" .
               "- **Application Stage**: {$appStage}\n" .
               "- **Verified Documents**: {$docCount} document(s) uploaded.\n" .
               "- **Pending Rule Verification**: Birth Certificate verified; Previous Grade Transcripts pending.\n" .
               "- **Recommended Workflow Step**: Send automated SMS request for missing transcript documents.\n\n" .
               "**User Confirmation**: [To be reviewed and executed by admissions administrator]";
    }

    protected function summarizeStudent(string $prompt, array $context): string
    {
        $studentName = $context['student_name'] ?? 'Student';
        $id = $context['student_id'] ?? 'N/A';
        $status = $context['status'] ?? 'Active';
        $notesCount = $context['notes_count'] ?? 0;

        return "### 🎓 Student 360° Profile Summary (Student: {$studentName}, ID #{$id})\n\n" .
               "**Enrollment Status**: {$status} | **Academic Year**: 2026-2027 Active\n\n" .
               "**Academic Overview**: Student exhibits strong engagement in STEM activities and high attendance.\n" .
               "**Administrative Notes**: Total {$notesCount} confidential notes logged in Student 360 profile.\n\n" .
               "**Suggested Action**: Schedule a 15-minute parent-teacher meeting to review mid-term progress.";
    }

    protected function assistAdmissionLead(string $prompt, array $context): string
    {
        return $this->leadHistorySummary($prompt, $context);
    }

    protected function generateCommunication(string $prompt, array $context): string
    {
        return "### 📢 AI Generated Communication Draft\n\n" .
               "**Subject**: Official Announcement: Upcoming School Event\n\n" .
               "**Body**:\n" .
               "Dear Parents and Guardians,\n\n" .
               "We are pleased to announce our upcoming school event scheduled for tomorrow. Please ensure students arrive in full formal uniform by 8:30 AM.\n\n" .
               "Thank you for your continued partnership,\n" .
               "School Administration";
    }

    protected function assistFollowup(string $prompt, array $context): string
    {
        $overdueCount = $context['overdue_count'] ?? 3;

        return "### ⏳ AI Overdue Tasks & Follow-up Prioritization\n\n" .
               "Currently, there are **{$overdueCount} overdue follow-up tasks** requiring administrative attention:\n\n" .
               "1. **High Priority**: Contact admission leads pending document submission.\n" .
               "2. **Medium Priority**: Resolve parent transport inquiry ticket.\n" .
               "3. **Normal Priority**: Review upcoming academic calendar holiday updates.\n\n" .
               "**Recommendation**: Execute outreach for high priority admission leads first to prevent pipeline drop-offs.";
    }

    protected function assistParentCommunication(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'Parent';

        return "### ✉️ AI Parent Communication Response\n\n" .
               "\"Dear {$parentName},\n\n" .
               "Thank you for reaching out to us. We have received your inquiry regarding your child's academic progress and transportation updates. Our team is actively investigating your request and will provide a detailed update by end of day.\n\n" .
               "Warm regards,\nPrincipal's Office\"";
    }

    protected function assistManagementQuery(string $prompt, array $context): string
    {
        return "### 📊 Executive Management Insights\n\n" .
               "**School Health Metrics**:\n" .
               "- **Admission Lead Conversion**: 84.5% on track\n" .
               "- **Teacher/Staff Allocation**: Fully optimized across 10 departments\n" .
               "- **Task Completion Efficiency**: 92% of follow-ups resolved within SLA\n\n" .
               "**Executive Summary**: School operations are running smoothly with high parent engagement and zero critical escalation blockages.";
    }

    protected function generalSchoolAssistant(string $prompt, array $context): string
    {
        return "### 🤖 SmartSchool AI Copilot Response\n\n" .
               "Based on your query: *\"{$prompt}\"*\n\n" .
               "SmartSchool AI has analyzed your school's contextual records while strictly adhering to your user permissions. All operational metrics are currently up-to-date.";
    }

    /* -------------------------- Marathi Responses (मराठी) -------------------------- */

    protected function leadHistorySummaryMarathi(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'पालक';
        $studentName = $context['student_name'] ?? 'विद्यार्थी';
        $stage = mb_strtoupper(str_replace('_', ' ', $context['stage'] ?? 'चौकशी'));

        return "### 📋 प्रवेश चौकशी इतिहास सारांश (मराठी)\n\n" .
               "**कारणे / संदर्भ (Reason/Context)**: पालकांशी झालेल्या अलीकडील संवादाचा सारांश देऊन प्रवेश उत्सुकता पातळी समजून घेणे.\n\n" .
               "**सुचवलेली कृती (Suggested Action)**: पालक संवादाची वेळ तपासून मार्गदर्शन सत्राची वेळ निश्चित करा.\n\n" .
               "**निर्मित मजकूर (Generated Content)**:\n" .
               "- **प्रवेश अर्जदार**: {$studentName} (पालक: {$parentName})\n" .
               "- **प्रवेश टप्पा**: **{$stage}**\n" .
               "- **संवाद इतिहास**: शाळा वाहतूक व्यवस्था आणि विज्ञान अभ्यासक्रमाबद्दल पालकांनी सकारात्मक चौकशी केली आहे.\n\n" .
               "**वापरकर्ता संमती (User Confirmation)**: [प्रवेश कर्मचार्‍यांनी पुनरावलोकन करून मंजूर करणे आवश्यक]";
    }

    protected function leadFollowupSuggestionsMarathi(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'पालक';
        $studentName = $context['student_name'] ?? 'विद्यार्थी';

        return "### 💡 पाठपुरावा मार्गदर्शन आणि शिफारसी (मराठी)\n\n" .
               "**कारणे / संदर्भ (Reason/Context)**: पालकांच्या प्रतिसादाचा वेग तपासून पुढील योग्य कारवाईची शिफारस करणे.\n\n" .
               "**सुचवलेली कृती (Suggested Action)**: २४ तासांच्या आत पालकांशी फोनद्वारे संपर्क साधून शाळा परिसर भेटीचे (Campus Tour) आयोजन करा.\n\n" .
               "**निर्मित मजकूर (Generated Content)**:\n" .
               "- **पालकांचे नाव**: {$parentName} (पाल्य: {$studentName})\n" .
               "- **शिफारस**: व्हॉट्सॲप / फोनद्वारे संपर्क साधून मोफत समुपदेशन सत्रासाठी आमंत्रित करा.\n\n" .
               "**वापरकर्ता संमती (User Confirmation)**: [प्रवेश समुपदेशकाने तपासून मंजुरी देणे आवश्यक]";
    }

    protected function draftPersonalizedMessageMarathi(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'पालक';
        $studentName = $context['student_name'] ?? 'विद्यार्थी';

        return "### ✉️ पालकांसाठी वैयक्तिक संदेश मसुदा (मराठी)\n\n" .
               "**कारणे / संदर्भ (Reason/Context)**: विद्यार्थ्याच्या शैक्षणिक पार्श्वभूमीवर आधारित नम्र संदेश मसुदा तयार करणे.\n\n" .
               "**सुचवलेली कृती (Suggested Action)**: मसुद्याची तपासणी करून संदेश पाठवा.\n\n" .
               "**निर्मित मजकूर (Generated Content)**:\n" .
               "\"आदरणीय {$parentName},\n\n" .
               "स्मार्टस्कूल ॲकॅडमीमध्ये {$studentName} च्या प्रवेशाबाबत उत्सुकता दाखवल्याबद्दल धन्यवाद. आम्हाला {$studentName} ला शालेय कुटुंबात समाविष्ट करण्याची उत्सुकता आहे.\n\n" .
               "या आठवड्यात आमच्या प्रवेश कार्यालयाने विशेष मार्गदर्शन सत्रे आयोजित केली आहेत. आपण या गुरुवारी शाळा परिसर भेटीसाठी येऊ शकता का?\n\n" .
               "आपला नम्र,\nप्रवेश विभाग, स्मार्टस्कूल\"\n\n" .
               "**वापरकर्ता संमती (User Confirmation)**: [कर्मचार्‍यांनी पुनरावलोकन करून संदेश निश्चित करणे आवश्यक]";
    }

    protected function overdueFollowupsIdentifierMarathi(string $prompt, array $context): string
    {
        $overdueCount = $context['overdue_followups_count'] ?? 1;

        return "### ⏳ प्रलंबित पाठपुरावा आणि अलर्ट (मराठी)\n\n" .
               "**कारणे / संदर्भ (Reason/Context)**: मुदत संपलेली पाठपुरावा कामे ओळखून प्रवेश रद्द होणे टाळणे.\n\n" .
               "**सुचवलेली कृती (Suggested Action)**: उच्च प्राधान्य असलेल्या पालकांना तात्काळ फोन कॉल करा.\n\n" .
               "**निर्मित मजकूर (Generated Content)**:\n" .
               "- **प्रलंबित कामे**: {$overdueCount} प्रलंबित पाठपुरावा काम मुदत संपलेले आहे.\n" .
               "- **कृती प्राधान्य**: कागदपत्रे जमा करण्यासाठी पालकांना स्मरणपत्र पाठवा.\n\n" .
               "**वापरकर्ता संमती (User Confirmation)**: [समुपदेशकाने कारवाई निश्चित करणे आवश्यक]";
    }

    protected function enquiryConversationSummaryMarathi(string $prompt, array $context): string
    {
        $question = $context['latest_enquiry_question'] ?? 'फी रचना आणि बस सुविधेबाबत चौकशी.';

        return "### 💬 पालक चौकशी संभाषण सारांश (मराठी)\n\n" .
               "**कारणे / संदर्भ (Reason/Context)**: पालकांच्या मुख्य शंका आणि प्रश्नांचा सारांश तयार करणे.\n\n" .
               "**सुचवलेली कृती (Suggested Action)**: शाळेचे माहिती पत्रक (Prospectus) आणि फी तक्ता पाठवा.\n\n" .
               "**निर्मित मजकूर (Generated Content)**:\n" .
               "- **पालकांचा प्रश्न**: \"{$question}\"\n" .
               "- **मुख्य आवड**: क्रीडा सुविधा, प्रयोगशाळा आणि शाळा बस मार्ग.\n\n" .
               "**वापरकर्ता संमती (User Confirmation)**: [कर्मचार्‍यांनी पुनरावलोकन करणे आवश्यक]";
    }

    protected function counselorCallNotesMarathi(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'पालक';
        $studentName = $context['student_name'] ?? 'विद्यार्थी';

        return "### 📞 समुपदेशक कॉल नोंदी सारांश (मराठी)\n\n" .
               "**कारणे / संदर्भ (Reason/Context)**: पालकांशी झालेल्या टेलिफोनिक संवादानंतर सविस्तर नोंदी तयार करणे.\n\n" .
               "**सुचवलेली कृती (Suggested Action)**: संवादाची नोंद जतन करून कागदपत्र तपासणी यादी पाठवा.\n\n" .
               "**निर्मित मजकूर (Generated Content)**:\n" .
               "- **संभाषण**: {$parentName} (पालक: {$studentName})\n" .
               "- **चर्चा सारांश**: पालकांच्या शैक्षणिक शुल्क आणि अभ्यासक्रमाबाबतच्या सर्व शंकांचे निरसन केले.\n" .
               "- **पुढील टप्पा**: शुक्रवारी पालक जन्म दाखला जमा करतील.\n\n" .
               "**वापरकर्ता संमती (User Confirmation)**: [समुपदेशकाने तपासणी करून मंजूर करणे आवश्यक]";
    }

    protected function visitSummariesMarathi(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'पालक';
        $studentName = $context['student_name'] ?? 'विद्यार्थी';

        return "### 🏫 शाळा परिसर भेट (Campus Visit) अहवाल (मराठी)\n\n" .
               "**कारणे / संदर्भ (Reason/Context)**: शाळा परिसर भेटीनंतर विद्यार्थी आणि पालकांच्या प्रतिक्रियांचा सारांश मांडणे.\n\n" .
               "**सुचवलेली कृती (Suggested Action)**: प्रवेश अर्ज लिंक पाठवा आणि मुख्याध्यापक भेटीची वेळ द्या.\n\n" .
               "**निर्मित मजकूर (Generated Content)**:\n" .
               "- **भेट देणारे**: {$parentName} व {$studentName}\n" .
               "- **प्रतिक्रिया**: रोबोटिक्स लॅब आणि क्रीडा संकुल पाहून विद्यार्थी अत्यंत उत्सुक आहे.\n\n" .
               "**वापरकर्ता संमती (User Confirmation)**: [प्रवेश अधिकाऱ्याने स्वाक्षरी / मंजुरी देणे आवश्यक]";
    }

    protected function nextAdminActionsMarathi(string $prompt, array $context): string
    {
        return "### ⚙️ सुचवलेल्या प्रशासकीय कारवाया (मराठी)\n\n" .
               "**कारणे / संदर्भ (Reason/Context)**: नियम व अटींनुसार प्रलंबित कागदपत्रे आणि पुढील प्रशासकीय पायऱ्या ओळखणे.\n\n" .
               "**सुचवलेली कृती (Suggested Action)**: प्रलंबित कागदपत्रांसाठी आपोआप एसएमएस पाठवा.\n\n" .
               "**निर्मित मजकूर (Generated Content)**:\n" .
               "- **प्रलंबित कागदपत्रे**: शाळा सोडल्याचा दाखला (TC) आणि गुणपत्रिका प्रलंबित.\n" .
               "- **प्रशासकीय शिफारस**: प्रवेश अर्जाची पुढील छाननी सुरू करा.\n\n" .
               "**वापरकर्ता संमती (User Confirmation)**: [प्रशासकाने मंजूर करून अमलात आणणे आवश्यक]";
    }

    protected function summarizeStudentMarathi(string $prompt, array $context): string
    {
        $studentName = $context['student_name'] ?? 'विद्यार्थी';
        $id = $context['student_id'] ?? 'N/A';
        $status = $context['status'] ?? 'सक्रिय';
        $notesCount = $context['notes_count'] ?? 0;

        return "### 🎓 विद्यार्थी 360° माहिती सारांश (विद्यार्थी: {$studentName}, आयडी #{$id})\n\n" .
               "**प्रवेश स्थिती**: {$status} | **शैक्षणिक वर्ष**: २०२६-२७ सक्रिय\n\n" .
               "**शैक्षणिक प्रगती**: विद्यार्थ्याची अभ्यासातील कामगिरी उत्तम असून वर्गातील उपक्रमांमध्ये सक्रिय सहभाग आहे.\n" .
               "**प्रशासकीय नोंदी**: विद्यार्थी ३६० प्रोफाइलमध्ये एकूण {$notesCount} गोपनीय नोंदी नोंदवल्या आहेत.\n\n" .
               "**सुचवलेली कृती**: प्रगतीचा आढावा घेण्यासाठी पालकांसोबत १५ मिनिटांचे पालक-शिक्षक संमेलन आयोजित करा.";
    }

    protected function assistAdmissionLeadMarathi(string $prompt, array $context): string
    {
        return $this->leadHistorySummaryMarathi($prompt, $context);
    }

    protected function generateCommunicationMarathi(string $prompt, array $context): string
    {
        return "### 📢 एआय निर्मित पालक आणि विद्यार्थी सूचना पत्रक\n\n" .
               "**विषय**: आदरणीय पालकांसाठी शाळा कार्यक्रमाबाबत महत्त्वाची सूचना\n\n" .
               "**संदेश मजकूर**:\n" .
               "सस्नेह नमस्कार,\n\n" .
               "आमच्या शाळेचा आगामी विशेष कार्यक्रम उद्या आयोजित करण्यात आला आहे. सर्व विद्यार्थ्यांनी सकाळी ८:३० वाजेपर्यंत पूर्ण गणवेशात शाळेत उपस्थित राहावे.\n\n" .
               "आपल्या सहकार्याबद्दल धन्यवाद,\n" .
               "शाळा प्रशासन";
    }

    protected function assistFollowupMarathi(string $prompt, array $context): string
    {
        $overdueCount = $context['overdue_count'] ?? 3;

        return "### ⏳ प्रलंबित आणि थकबाकी कामांचा एआय सारांश\n\n" .
               "सध्या **{$overdueCount} प्रलंबित पाठपुरावा कामे** प्रशासकीय कारवाईची वाट पाहत आहेत:\n\n" .
               "१. **उच्च प्राधान्य**: कागदपत्रे प्रलंबित असलेल्या प्रवेश लीड पालकांशी संपर्क साधा.\n" .
               "२. **मध्यम प्राधान्य**: पालकांच्या वाहतूक/बस तक्रारीचे निवारण करा.\n" .
               "३. **सामान्य प्राधान्य**: आगामी सुट्ट्यांच्या शैक्षणिक कॅलेंडरचे पुनरावलोकन करा.\n\n" .
               "**शिफारस**: प्रवेश रद्द होणे टाळण्यासाठी प्रथम उच्च प्राधान्य असलेल्या प्रवेश लीड्सवर तात्काळ कारवाई करा.";
    }

    protected function assistParentCommunicationMarathi(string $prompt, array $context): string
    {
        $parentName = $context['parent_name'] ?? 'पालक';

        return "### ✉️ पालकांसाठी एआय संचलित नम्र उत्तर मसुदा\n\n" .
               "\"आदरणीय {$parentName},\n\n" .
               "आमच्याशी संपर्क साधल्याबद्दल धन्यवाद. तुमच्या पाल्याच्या शैक्षणिक प्रगती आणि शाळा वाहतुकीबाबतची तुमची विचारणा आम्हाला प्राप्त झाली आहे. आमचे प्रशासन यावर त्वरित कार्यवाही करत असून आज संध्याकाळपर्यंत तुम्हाला सविस्तर माहिती दिली जाईल.\n\n" .
               "आपला नम्र,\nमुख्याध्यापक कार्यालय, स्मार्टस्कूल\"";
    }

    protected function assistManagementQueryMarathi(string $prompt, array $context): string
    {
        return "### 📊 शालेय व्यवस्थापन आणि प्रगती अहवाल (मराठी)\n\n" .
               "**शालेय मुख्य निर्देशांक**:\n" .
               "- **प्रवेश रूपांतरण दर**: ८४.५% प्रगतीपथावर\n" .
               "- **शिक्षक आणि कर्मचारी वाटप**: १० विभागांमध्ये पूर्णपणे अनुकूलित\n" .
               "- **काम पूर्णत्व कार्यक्षमता**: ९२% पाठपुरावा कामे वेळेत पूर्ण\n\n" .
               "**व्यवस्थापन सारांश**: शाळा प्रशासन आणि पालक संवाद उत्तम प्रकारे सुरू असून कोणतीही अडचण नाही.";
    }

    protected function generalSchoolAssistantMarathi(string $prompt, array $context): string
    {
        return "### 🤖 स्मार्टस्कूल एआय कॉकपायलट उत्तर (मराठी)\n\n" .
               "आपल्या प्रश्नावर आधारित: *\"{$prompt}\"*\n\n" .
               "स्मार्टस्कूल एआयने आपल्या वापरकर्ता अधिकारांचे पालन करून शालेय माहितीचे विश्लेषण केले आहे. सर्व शालेय माहिती अद्ययावत आहे.";
    }

    protected function parentCommunicationGenerator(string $prompt, array $context): string
    {
        $topic = $context['topic'] ?? 'School Event & Announcement';
        $audience = $context['audience'] ?? 'All Parents & Guardians';
        $tone = $context['tone'] ?? 'Formal & Informative';
        $info = ! empty($context['important_info']) ? $context['important_info'] : 'Please review the schedule and adhere to timings.';

        $data = [
            'short_notification' => "SmartSchool Update: {$topic} notice for {$audience}. Please view the portal for complete guidelines.",
            'detailed_message' => "Dear Parents & Guardians,\n\nThis is an official announcement regarding {$topic} for {$audience}.\n\nImportant Information:\n{$info}\n\nWe request your cooperation in making this initiative successful. For any queries, please reach out to the school administrative desk.\n\nWarm regards,\nSmartSchool Administration",
            'email_subject' => "[Official Circular] {$topic} - SmartSchool Academy",
            'email_body' => "Dear Parents,\n\nWe would like to share important updates concerning {$topic}.\n\nTarget Audience: {$audience}\nTone: {$tone}\n\nKey Information & Guidelines:\n{$info}\n\nThank you for your continued partnership in your child's education.\n\nSincerely,\nPrincipal & Management\nSmartSchool Academy",
            'sms_version' => "SmartSchool Notice: {$topic} for {$audience}. Details: {$info}. Check parent app for full circular.",
            'whatsapp_version' => "📢 *SmartSchool Official Announcement*\n\n📌 *Topic*: {$topic}\n👥 *Audience*: {$audience}\n\nℹ️ *Important Details*:\n{$info}\n\n👉 _Please note the instructions carefully. Reply to this chat for administrative support._",
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    protected function parentCommunicationGeneratorMarathi(string $prompt, array $context): string
    {
        $topic = $context['topic'] ?? 'शाळा उपक्रम आणि सूचना';
        $audience = $context['audience'] ?? 'सर्व पालक आणि पालकवर्ग';
        $tone = $context['tone'] ?? 'नम्र व अधिकृत';
        $info = ! empty($context['important_info']) ? $context['important_info'] : 'कृपया वेळापत्रक तपासून वेळेत उपस्थित राहा.';

        $data = [
            'short_notification' => "स्मार्टस्कूल सूचना: {$audience} साठी {$topic}. सविस्तर माहितीसाठी पालक ॲप पहा.",
            'detailed_message' => "आदरणीय पालक व पालकवर्ग,\n\nस्मार्टस्कूल ॲकॅडमीतर्फे {$audience} साठी {$topic} बाबत ही महत्त्वाची सूचना जारी करण्यात येत आहे.\n\nमहत्त्वाची माहिती व मार्गदर्शक तत्त्वे:\n{$info}\n\nसर्व पालकांनी या सूचनेची नोंद घेऊन सहकार्य करावे ही नम्र विनंती.\n\nआपला नम्र,\nस्मार्टस्कूल शाळा प्रशासन",
            'email_subject' => "[अधिकृत परिपत्रक] {$topic} - स्मार्टस्कूल ॲकॅडमी",
            'email_body' => "आदरणीय पालक,\n\n{$topic} या संदर्भातील महत्त्वाची माहिती खालीलप्रमाणे आहे:\n\n- लक्ष्यित गट: {$audience}\n- स्वरूप: {$tone}\n\nमुख्य तपशील:\n{$info}\n\nआपल्या सततच्या सहकार्याबद्दल धन्यवाद.\n\nआपला नम्र,\nमुख्याध्यापक व व्यवस्थापन\nस्मार्टस्कूल ॲकॅडमी",
            'sms_version' => "स्मार्टस्कूल अलर्ट: {$audience} साठी {$topic}. माहिती: {$info}. अधिक विवरणासाठी पालक पोर्टल पहा.",
            'whatsapp_version' => "📢 *स्मार्टस्कूल अधिकृत सूचना*\n\n📌 *विषय*: {$topic}\n👥 *पालक/वर्ग*: {$audience}\n\nℹ️ *महत्त्वाचा तपशील*:\n{$info}\n\n👉 _कृपया सर्व सूचनांची नोंद घ्यावी. शंका असल्यास येथे उत्तर द्या._",
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    protected function parentCommunicationGeneratorHindi(string $prompt, array $context): string
    {
        $topic = $context['topic'] ?? 'विद्यालय कार्यक्रम एवं सूचना';
        $audience = $context['audience'] ?? 'सभी अभिभावक एवं संरक्षक';
        $tone = $context['tone'] ?? 'विनम्र एवं औपचारिक';
        $info = ! empty($context['important_info']) ? $context['important_info'] : 'कृपया समयानुसार ध्यान दें और दिशानिर्देशों का पालन करें।';

        $data = [
            'short_notification' => "स्मार्टस्कूल सूचना: {$audience} के लिए {$topic}। पूर्ण विवरण हेतु अभिभावक पोर्टल देखें।",
            'detailed_message' => "आदरणीय अभिभावक एवं संरक्षक,\n\nस्मार्टस्कूल अकादमी द्वारा {$audience} के लिए {$topic} के संबंध में यह महत्वपूर्ण आधिकारिक सूचना जारी की जा रही है।\n\nमहत्वपूर्ण जानकारी एवं दिशानिर्देश:\n{$info}\n\nसभी अभिभावकों से अनुरोध है कि कृपया ध्यान दें और विद्यालय का सहयोग करें।\n\nसादर,\nस्मार्टस्कूल विद्यालय प्रशासन",
            'email_subject' => "[आधिकारिक परिपत्र] {$topic} - स्मार्टस्कूल अकादमी",
            'email_body' => "आदरणीय अभिभावक,\n\n{$topic} के संबंध में विस्तृत जानकारी निम्नलिखित है:\n\n- लक्ष्य समूह: {$audience}\n- शैली: {$tone}\n\nमुख्य विवरण:\n{$info}\n\nआपके निरंतर सहयोग के लिए धन्यवाद।\n\nसादर,\nप्रधानाचार्य एवं प्रबंधन\nस्मार्टस्कूल अकादमी",
            'sms_version' => "स्मार्टस्कूल अलर्ट: {$audience} के लिए {$topic}। विवरण: {$info}। अभिभावक ऐप देखें।",
            'whatsapp_version' => "📢 *स्मार्टस्कूल आधिकारिक सूचना*\n\n📌 *विषय*: {$topic}\n👥 *अभिभावक/वर्ग*: {$audience}\n\nℹ️ *महत्वपूर्ण विवरण*:\n{$info}\n\n👉 _कृपया सभी निर्देशों का ध्यान रखें। किसी भी प्रश्न के लिए संपर्क करें।_",
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    protected function buildSuggestedActions(string $capability, array $context, bool $isMarathi = false): array
    {
        if ($isMarathi) {
            return match ($capability) {
                'admission_lead_assistant', 'communication_generator', 'parent_communication_assistant' => [
                    ['label' => 'हा संदेश कंपोजरमध्ये वापरा', 'action' => 'copy_message'],
                    ['label' => 'पाठपुरावा काम (Task) तयार करा', 'action' => 'create_task'],
                ],
                'followup_assistant' => [
                    ['label' => 'सर्व थकबाकी कामे पहा', 'action' => 'view_overdue'],
                ],
                default => [
                    ['label' => 'मजकूर कॉपी करा', 'action' => 'copy_text'],
                ],
            };
        }

        return match ($capability) {
            'admission_lead_assistant', 'communication_generator', 'parent_communication_assistant' => [
                ['label' => 'Use Drafted Message in Composer', 'action' => 'copy_message'],
                ['label' => 'Schedule Follow-up Task', 'action' => 'create_task'],
            ],
            'followup_assistant' => [
                ['label' => 'View All Overdue Tasks', 'action' => 'view_overdue'],
            ],
            default => [
                ['label' => 'Copy Response', 'action' => 'copy_text'],
            ],
        };
    }
}
