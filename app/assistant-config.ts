export let assistantId = "asst_Q4OcqBgbIyJfQ7afZD7ZoYMX"; // set your assistant ID here

if (assistantId === "") {
  assistantId = process.env.OPENAI_ASSISTANT_ID;
}
