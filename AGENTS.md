## Codebase discovery

Prefer PhpStorm MCP tools for understanding code:
- Use search_symbol to locate classes, methods, and fields.
- Use get_symbol_info for declarations, types, and documentation.
- Use analyze_calls for callers, callees, and call dependencies.
- When Graphify is available and its index is current, use it for
  conceptual discovery and relationships across files.

Use rg for literal strings, configuration, templates, dynamic wiring,
and gaps in indexed coverage. Avoid broad text searches as the first
step for symbol or call-relationship questions.

Before editing, read the current working-tree source. Indexed results
may omit recent changes. If an indexed tool is unavailable, state that
briefly and use targeted text search.

## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

Invoke Graphify in Codex with `$graphify` (or select it through `/skills` in the CLI/IDE). The repository skill is discoverable at `.agents/skills/graphify`.

When the user invokes `$graphify`, use the installed graphify skill or instructions before doing anything else. If `/graphify` arrives as ordinary message text, treat it as the same request; AGENTS.md cannot register a native slash command.

Rules:
- When graphify-out/graph.json exists, run `graphify query "<question>"` before your first grep, search or source-file read for a codebase question, and put this rule in every subagent prompt that explores code. Read raw files only for lines the graph or an earlier read already located. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- Dirty graphify-out/ files are expected after hooks or incremental updates; dirty graph files are not a reason to skip graphify. Only skip graphify if the task is about stale or incorrect graph output, or the user explicitly says not to use it.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).
- A committed graphify-out/graph.json is rewritten whole on every build, so a large diff to it in a pull request is expected. Do not flag it as churn or suggest skipping the graph rebuild or its hooks.
