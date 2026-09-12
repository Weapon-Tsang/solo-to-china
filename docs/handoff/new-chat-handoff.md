# SoloToChina 后续任务交接

当前工作区：`C:\Users\Mloong\Documents\ChatGPT\solo-to-china`。先看 [当前进度](current-progress.md)、[42 项台账](../upgrades/frontend-experience-v1.md)、[QA](../qa/frontend-upgrade-v1.md)；archive 中旧交接仅作历史。

用户已确认并要求实施完整 Frontend Upgrade v1 任务书，阶段 0–4 的本地代码、契约、测试、视觉复查和安装包已完成。开发基线是 main 的 f44ce1092ced93dfb47d9b3eae83d0d5e4b97086，提交范围包含保留的正确原有修改和本轮成果。验收后用户已授权提交并推送 origin/main；没有 reset 或生产部署。继续工作前先检查实时 git status/diff/log，不要按旧版本或审查快照回退。

版本：Parent 0.33.0 / Child 0.12.0 / Tools 0.26.0 / Registry 1.4.0 / Contract 2.1.0 / Publish Package 1.0.0。

稳定入口：
- `pwsh -File scripts/verify-upgrade.ps1`：PowerShell 7，静态/契约/图像。可加 `-BaseUrl` 执行现有 runtime。
- `scripts/start-preview.ps1`：默认 parent+child+plugin；`-ParentOnly` 或 `-NoTools`。预览中有真实长文章、8 城市、双工具及动态 Share fixture。
- 浏览器 scripts/verify-experience-browser.js、verify-tool-interactions.js、verify-experience-edges.js、verify-final-states.js、verify-editor-browser.js、verify-release-boundaries.js。本轮本地端口 9411/9412/9413；新会话不要假设进程仍活着。
- 本地测试 MU 路由 stc-test 仅存在于临时 Playground。重复上传套件要重置其测试限流桶，不能削弱产品限流。
- PHP CLI 缺失，以 PHP 8.3.32 TOKEN_PARSE 全量解析 42 个 PHP 文件及真实 WordPress harness 替代。不要报告原生 php -l 已运行。
- Authoring Registry 是 `wp-content/themes/solo-to-china/content-contract/component-registry.v1.json`。修改它后运行生成器；不要分叉生成契约。

核心边界：父主题负责语义和 CMS 契约、子主题视觉、插件工具。CMS 决定文章块选择/顺序；禁止自动插入整组工具或按分类重建文章。目的地目录来源和识别置信度独立，模型不能升级 UNKNOWN 为 VERIFIED。现有 9 条记录到达细节大多未核实，绝不能编造入口或下车点。没有 provider 配置时准确显示不可用。本轮测试全部使用本地 stub，无实际付费识别请求。

下一步只有明确外部待办或新用户需求：真实网关/隐私/预算配置、独立 CMS draft 联调、真实资料与票务规则维护、用户授权的 staging/production 安装和缓存策略、物理设备/线上性能验收。部署顺序和回滚见 [部署文档](../deployment/frontend-upgrade-v1.md)。未经授权不要发布生产环境。

本地安装包和 SHA-256 见 dist/release-manifest.txt。生成包来自 dirty 工作区，不能以基础 HEAD 单独重建本轮成果。QA JSON 和未经编辑的截图在 docs/qa/evidence；更完整本机原始日志位于被忽略的 output/playwright。

## Suggested New Chat Opening Message

请读取当前进度、实施台账和 QA，检查实时工作区与已有修改。在已完成的 0.33.0/0.12.0/0.26.0 基础上继续处理我指定的外部联调或新需求；不要从旧审查快照重做本轮升级。

## Fixed Information Architecture

主导航仍为 Home、Survival Kit、City Guides、Attraction Guides、Planner、Tools、FAQ。Contact、About、Privacy Policy、Terms of Use 在既有辅助/页脚路径。Tools 包含 Find This Place、Taxi Card 与辅助 Ticket Booking Window；不要把票务提升成独立提醒或登录产品。

## Do Not Start Without Explicit Approval

用户本轮未授权生产部署。生产发布、真实付费服务启用、外部 CMS 发布按用户后续明确授权处理。本轮本地改造方向已经全部批准，不需要重新请求设计或代码实施许可。

## Development Style For Next Chat

先检查实际分支、dirty 和代码，保留正确实现。父主题/子主题/插件遵守既有边界；修改能力时同步单一 Registry 和生成器产物。运行与变更相关的真实检查，记录未测/外部依赖，不把文档、mock 或截图数量当成完整生产验收。
